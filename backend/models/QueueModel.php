<?php
declare(strict_types=1);

namespace Canteen\Models;

use PDO;

final class QueueModel
{
    public function __construct(private PDO $pdo)
    {
    }

    public function addToQueue(
        int $staffId,
        string $shiftType,
        string $mealLabel,
        ?string $dietNotes,
        ?int $servedBy,
        string $receiptStatus = 'Receipt not printed',
        ?string $servedAt = null
    ): int {
        $this->ensureReceiptStatusColumn();

        if ($servedAt !== null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO serving_queue (staff_id, shift_type, meal_label, diet_notes, receipt_status, served_by, served_at)
                 VALUES (:staff_id, :shift_type, :meal_label, :diet_notes, :receipt_status, :served_by, :served_at)'
            );
            $stmt->execute([
                'staff_id' => $staffId,
                'shift_type' => $shiftType,
                'meal_label' => $mealLabel,
                'diet_notes' => $dietNotes,
                'receipt_status' => $receiptStatus,
                'served_by' => $servedBy,
                'served_at' => $servedAt,
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO serving_queue (staff_id, shift_type, meal_label, diet_notes, receipt_status, served_by)
                 VALUES (:staff_id, :shift_type, :meal_label, :diet_notes, :receipt_status, :served_by)'
            );
            $stmt->execute([
                'staff_id' => $staffId,
                'shift_type' => $shiftType,
                'meal_label' => $mealLabel,
                'diet_notes' => $dietNotes,
                'receipt_status' => $receiptStatus,
                'served_by' => $servedBy,
            ]);
        }

        return (int) $this->pdo->lastInsertId();
    }

    public function hasServedOnDate(int $staffId, string $date): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM serving_queue WHERE staff_id = :staff_id AND DATE(served_at) = :served_date LIMIT 1');
        $stmt->execute([
            'staff_id' => $staffId,
            'served_date' => $date,
        ]);
        return (bool) $stmt->fetchColumn();
    }

    public function listForDate(string $date, int $limit = 10, ?int $staffId = null, ?int $sinceId = null): array
    {
        $params = ['served_date' => $date];
        $staffClause = '';
        $sinceClause = '';

        $projectSelect = "(SELECT ms.project FROM meal_selection ms WHERE ms.staff_id = sq.staff_id AND ms.week_start_date <= DATE(sq.served_at) ORDER BY ms.week_start_date DESC LIMIT 1) AS meal_project";

        if ($staffId !== null) {
            $staffClause = ' AND sq.staff_id = :staff_id';
            $params['staff_id'] = $staffId;
        }

        if ($sinceId !== null) {
            $sinceClause = ' AND sq.id > :since_id';
            $params['since_id'] = $sinceId;
        }

        if ($staffClause !== '') {
            $sql = "SELECT sq.*, u.name, u.lastname, u.project, $projectSelect
                    FROM serving_queue sq
                    JOIN user u ON u.userid = sq.staff_id
                    WHERE DATE(sq.served_at) = :served_date$staffClause$sinceClause
                    ORDER BY sq.served_at ASC";
        } else {
            $limitValue = max(1, $limit);
            if ($sinceClause !== '') {
                $sql = "SELECT sq.*, u.name, u.lastname, u.project, $projectSelect
                        FROM serving_queue sq
                        JOIN user u ON u.userid = sq.staff_id
                        WHERE DATE(sq.served_at) = :served_date$sinceClause
                        ORDER BY sq.served_at ASC";
            } else {
                $sql = "SELECT * FROM (
                            SELECT sq.*, u.name, u.lastname, u.project, $projectSelect
                            FROM serving_queue sq
                            JOIN user u ON u.userid = sq.staff_id
                            WHERE DATE(sq.served_at) = :served_date
                            ORDER BY sq.served_at DESC
                            LIMIT $limitValue
                        ) AS recent
                        ORDER BY recent.served_at ASC";
            }
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $type);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQueueState(bool $forUpdate = false): array
    {
        $this->ensureQueueStateTable();
        $sql = 'SELECT id, last_audit_id FROM queue_state WHERE id = 1';
        if ($forUpdate) {
            $sql .= ' FOR UPDATE';
        }

        $stmt = $this->pdo->query($sql);
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;

        if (!$row) {
            $this->pdo->prepare('INSERT INTO queue_state (id, last_audit_id) VALUES (1, 0)')->execute();
            return ['id' => 1, 'last_audit_id' => 0];
        }

        return $row;
    }

    /**
     * Ensure the queue state table exists and a seed row is present before any transactional work.
     * This prevents DDL from implicitly committing an open transaction during ingest.
     */
    public function primeQueueState(): void
    {
        $this->ensureQueueStateTable();
        $this->pdo->prepare('INSERT IGNORE INTO queue_state (id, last_audit_id) VALUES (1, 0)')->execute();
    }

    public function updateQueueState(int $lastAuditId): void
    {
        $this->ensureQueueStateTable();
        $stmt = $this->pdo->prepare('UPDATE queue_state SET last_audit_id = :last_id, updated_at = NOW() WHERE id = 1');
        $stmt->execute(['last_id' => $lastAuditId]);
    }

    private function ensureReceiptStatusColumn(): void
    {
        static $checked = false;
        if ($checked) {
            return;
        }

        $checked = true;
        $columnStmt = $this->pdo->query("SHOW COLUMNS FROM serving_queue LIKE 'receipt_status'");
        if ($columnStmt && $columnStmt->fetch()) {
            return;
        }

        $this->pdo->exec("ALTER TABLE serving_queue ADD COLUMN receipt_status VARCHAR(64) NOT NULL DEFAULT 'Receipt not printed'");
    }

    private function ensureQueueStateTable(): void
    {
        static $tableChecked = false;
        if ($tableChecked) {
            return;
        }
        $tableChecked = true;

        $this->pdo->exec('CREATE TABLE IF NOT EXISTS queue_state (
            id INT UNSIGNED NOT NULL PRIMARY KEY,
            last_audit_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
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
