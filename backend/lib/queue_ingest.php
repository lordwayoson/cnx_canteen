<?php
declare(strict_types=1);

namespace Canteen\Lib\QueueIngest;

use Canteen\Models\MealSelectionModel;
use Canteen\Models\QueueModel;
use Canteen\Models\UserModel;
use Canteen\Lib\Receipt;
use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Throwable;

/**
 * Ingest new ingress.badging rows into serving_queue using a durable cursor.
 *
 * @return array{processed_count:int,last_audit_id:int,processed:array,skipped:array}
 */
function ingestIngressAudit(
    PDO $canteenPdo,
    PDO $ingressPdo,
    UserModel $userModel,
    MealSelectionModel $mealModel,
    QueueModel $queueModel,
    DateTimeZone $tz
): array {
    $now = new DateTimeImmutable('now', $tz);
    $dayStart = $now->setTime(0, 0, 0)->format('Y-m-d H:i:s');
    $dayEnd = $now->setTime(0, 0, 0)->modify('+1 day')->format('Y-m-d H:i:s');
    $mapIngressUser = $ingressPdo->prepare(
        'SELECT employeeid, cardnumber FROM user_info WHERE userid = :userid LIMIT 1'
    );
    // Ensure the queue state table exists before opening a transaction to avoid
    // implicit commits caused by DDL while a transaction is active.
    $queueModel->primeQueueState();

    $canteenPdo->beginTransaction();
    try {
        $state = $queueModel->getQueueState(true);
        $lastId = (int) ($state['last_audit_id'] ?? 0);

        $stmt = $ingressPdo->prepare(
            'SELECT ID, userid, badged_date
               FROM badging
              WHERE ID > :lastId
                AND badged_date >= :dayStart
                AND badged_date < :dayEnd
              ORDER BY ID ASC'
        );
        $stmt->execute([
            'lastId' => $lastId,
            'dayStart' => $dayStart,
            'dayEnd' => $dayEnd,
        ]);

        $events = $stmt->fetchAll() ?: [];
        $processed = [];
        $skipped = [];
        $newCursor = $lastId;

        foreach ($events as $event) {
            $eventId = (int) ($event['ID'] ?? 0);
            $newCursor = max($newCursor, $eventId);

            try {
                $rawIngressUserId = isset($event['userid']) ? (int) $event['userid'] : 0;
                if ($rawIngressUserId === 0) {
                    $skipped[] = ['id' => $eventId, 'reason' => 'Missing userid'];
                    continue;
                }

                $mapIngressUser->execute(['userid' => $rawIngressUserId]);
                $ingressUser = $mapIngressUser->fetch();
                $mappedStaffId = isset($ingressUser['employeeid']) ? (int) $ingressUser['employeeid'] : 0;
                if ($mappedStaffId === 0) {
                    $skipped[] = [
                        'id' => $eventId,
                        'userid' => $rawIngressUserId,
                        'reason' => 'employeeid not found in ingress.user_info',
                    ];
                    continue;
                }

                $user = $userModel->findByCardOrUserId(null, $mappedStaffId);
                if (!$user) {
                    $skipped[] = [
                        'id' => $eventId,
                        'userid' => $mappedStaffId,
                        'reason' => 'Staff not found in canteen_db',
                    ];
                    continue;
                }

                $eventTime = null;
                try {
                    $eventTime = isset($event['badged_date']) && $event['badged_date']
                        ? new DateTimeImmutable((string) $event['badged_date'], $tz)
                        : new DateTimeImmutable('now', $tz);
                } catch (Throwable $timeError) {
                    $eventTime = new DateTimeImmutable('now', $tz);
                }

                $servedDate = $eventTime->format('Y-m-d');
                $fullName = trim(($user['name'] ?? '') . ' ' . ($user['lastname'] ?? ''));
                $project = strtolower((string) ($user['project'] ?? ''));
                $isSpecial = in_array($project, ['reserved', 'temporal'], true);

                if (!$isSpecial && $queueModel->hasServedOnDate($mappedStaffId, $servedDate)) {
                    $skipped[] = ['id' => $eventId, 'userid' => $mappedStaffId, 'reason' => 'Already served today'];
                    continue;
                }

                if ($isSpecial) {
                    $projectLabel = (string) ($user['project'] ?? 'Reserved');
                    $receiptText = sprintf(
                        "Canteen Meal\nProject: %s\nTime: %s",
                        $projectLabel,
                        $eventTime->format('Y-m-d H:i:s')
                    );
                    $printed = Receipt::printTicket($receiptText);
                    $receiptStatus = $printed ? 'Receipt printed' : 'Receipt not printed';

                    $queueModel->addToQueue(
                        $mappedStaffId,
                        'Day',
                        'N/A',
                        null,
                        null,
                        $receiptStatus,
                        $eventTime->format('Y-m-d H:i:s')
                    );

                    $processed[] = [
                        'id' => $eventId,
                        'userid' => $mappedStaffId,
                        'project' => $projectLabel,
                        'special' => true,
                        'receipt_status' => $receiptStatus,
                    ];
                    continue;
                }

                $meal = $mealModel->getMealForDate($mappedStaffId, $eventTime);
                if (!$meal || empty($meal['meal_label'])) {
                    $skipped[] = ['id' => $eventId, 'userid' => $mappedStaffId, 'reason' => 'No meal selection for today'];
                    continue;
                }

                $ticketText = sprintf(
                    "Canteen Meal\nName: %s\nStaff ID: %s\nProject: %s\nShift: %s\nMeal: %s\nDiet: %s\nQueued: %s",
                    $fullName !== '' ? $fullName : 'Staff',
                    $user['userid'],
                    $user['project'] ?? 'N/A',
                    $meal['shift_type'] ?? 'Day',
                    $meal['meal_label'] ?? 'Meal',
                    $meal['diet_notes'] ?? 'None',
                    $eventTime->format('Y-m-d H:i:s')
                );

                $printed = Receipt::printTicket($ticketText);
                $receiptStatus = $printed ? 'Receipt printed' : 'Receipt not printed';

                $queueModel->addToQueue(
                    $mappedStaffId,
                    (string) ($meal['shift_type'] ?? 'Day'),
                    (string) $meal['meal_label'],
                    $meal['diet_notes'] ?? null,
                    null,
                    $receiptStatus,
                    $eventTime->format('Y-m-d H:i:s')
                );

                $processed[] = [
                    'id' => $eventId,
                    'userid' => $mappedStaffId,
                    'full_name' => $fullName,
                    'project' => $user['project'] ?? null,
                    'meal' => $meal['meal_label'],
                    'shift_type' => $meal['shift_type'] ?? 'Day',
                    'receipt_status' => $receiptStatus,
                ];
            } catch (Throwable $eventError) {
                $skipped[] = ['id' => $eventId, 'reason' => $eventError->getMessage()];
                continue;
            }
        }

        $queueModel->updateQueueState($newCursor);
        $canteenPdo->commit();

        return [
            'processed_count' => count($processed),
            'last_audit_id' => $newCursor,
            'processed' => $processed,
            'skipped' => $skipped,
        ];
    } catch (Throwable $throwable) {
        if ($canteenPdo->inTransaction()) {
            $canteenPdo->rollBack();
        }
        throw $throwable;
    }
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
