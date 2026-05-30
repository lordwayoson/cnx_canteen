<?php
declare(strict_types=1);

namespace Canteen\Models;

use DateTimeImmutable;
use PDO;

final class MealSelectionModel
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Resolve today's meal for a staff member using the week-based columns (mon-sun).
     */
    public function getMealForStaff(int $staffId, string $shiftType, DateTimeImmutable $date): ?array
    {
        $dayKey = $this->dayKeyForDate($date);
        $stmt = $this->pdo->prepare(
            'SELECT * FROM meal_selection
             WHERE staff_id = :staff_id AND shift_type = :shift_type AND week_start_date <= :date
             ORDER BY week_start_date DESC LIMIT 1'
        );
        $stmt->execute([
            'staff_id' => $staffId,
            'shift_type' => $shiftType,
            'date' => $date->format('Y-m-d'),
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        $mealLabel = $row[$dayKey] ?? null;
        if ($mealLabel === null || $mealLabel === '') {
            return null;
        }

        return [
            'meal_label' => $mealLabel,
            'diet_notes' => $row['diet_notes'] ?? null,
            'shift_type' => $row['shift_type'],
            'week_start_date' => $row['week_start_date'],
        ];
    }

    /**
     * Resolve the meal for a specific calendar date, checking Day then Night rows.
     */
    public function getMealForDate(int $staffId, DateTimeImmutable $date): ?array
    {
        $dayMeal = $this->getMealForStaff($staffId, 'Day', $date);
        if ($dayMeal !== null) {
            return $dayMeal;
        }

        $nightMeal = $this->getMealForStaff($staffId, 'Night', $date);
        if ($nightMeal !== null) {
            return $nightMeal;
        }

        return null;
    }

    private function dayKeyForDate(DateTimeImmutable $date): string
    {
        $dayMap = [
            'Mon' => 'mon',
            'Tue' => 'tue',
            'Wed' => 'wed',
            'Thu' => 'thu',
            'Fri' => 'fri',
            'Sat' => 'sat',
            'Sun' => 'sun',
        ];

        return $dayMap[$date->format('D')] ?? 'mon';
    }

    public function getSummary(array $filters, ?string $reportType = null): array
    {
        $conditions = [];
        $params = [];
        if (!empty($filters['start_date'])) {
            $conditions[] = 'sq.served_at >= :start_date';
            $params['start_date'] = $filters['start_date'] . ' 00:00:00';
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 'sq.served_at <= :end_date';
            $params['end_date'] = $filters['end_date'] . ' 23:59:59';
        }
        if (!empty($filters['shift_type'])) {
            $conditions[] = 'sq.shift_type = :shift_type';
            $params['shift_type'] = $filters['shift_type'];
        }
        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $totals = [];
        $topMeals = [];
        $staff = [];
        $servedMeals = [];

        if ($reportType === null || $reportType === 'daily_totals') {
            $totalsStmt = $this->pdo->prepare(
                "SELECT DATE(served_at) as date, COUNT(*) as total FROM serving_queue sq $where GROUP BY DATE(served_at) ORDER BY DATE(served_at)"
            );
            $totalsStmt->execute($params);
            $totals = $totalsStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($reportType === null || $reportType === 'top_meals') {
            $topMealsStmt = $this->pdo->prepare(
                "SELECT meal_label, COUNT(*) as count FROM serving_queue sq $where GROUP BY meal_label ORDER BY count DESC LIMIT 10"
            );
            $topMealsStmt->execute($params);
            $topMeals = $topMealsStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($reportType === null) {
            $staffStmt = $this->pdo->prepare(
                "SELECT u.name, u.lastname, COUNT(*) as count FROM serving_queue sq JOIN user u ON u.userid = sq.staff_id $where GROUP BY sq.staff_id ORDER BY count DESC LIMIT 10"
            );
            $staffStmt->execute($params);
            $staff = $staffStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($reportType === null || $reportType === 'meals_served') {
            $servedMealWhere = $where ? $where . ' AND sq.meal_label IS NOT NULL' : 'WHERE sq.meal_label IS NOT NULL';
            $servedMealsStmt = $this->pdo->prepare(
                "SELECT DATE(sq.served_at) AS date, sq.meal_label, COUNT(*) AS count
                 FROM serving_queue sq
                 $servedMealWhere
                 GROUP BY DATE(sq.served_at), sq.meal_label
                 ORDER BY DATE(sq.served_at), sq.meal_label"
            );
            $servedMealsStmt->execute($params);
            $servedMeals = $servedMealsStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $selectedConditions = ['meal_label IS NOT NULL', "meal_label <> ''"];
        $selectedParams = [];
        if (!empty($filters['start_date'])) {
            $selectedConditions[] = 'selected_date >= :sel_start';
            $selectedParams['sel_start'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $selectedConditions[] = 'selected_date <= :sel_end';
            $selectedParams['sel_end'] = $filters['end_date'];
        }
        if (!empty($filters['shift_type'])) {
            $selectedConditions[] = 'shift_type = :sel_shift';
            $selectedParams['sel_shift'] = $filters['shift_type'];
        }
        $selectedWhere = 'WHERE ' . implode(' AND ', $selectedConditions);

        $selectedSql = "SELECT DATE(selected_date) AS date, meal_label, COUNT(*) AS count
                        FROM (
                            SELECT DATE_ADD(week_start_date, INTERVAL 0 DAY) AS selected_date, mon AS meal_label, shift_type FROM meal_selection
                            UNION ALL
                            SELECT DATE_ADD(week_start_date, INTERVAL 1 DAY) AS selected_date, tue AS meal_label, shift_type FROM meal_selection
                            UNION ALL
                            SELECT DATE_ADD(week_start_date, INTERVAL 2 DAY) AS selected_date, wed AS meal_label, shift_type FROM meal_selection
                            UNION ALL
                            SELECT DATE_ADD(week_start_date, INTERVAL 3 DAY) AS selected_date, thu AS meal_label, shift_type FROM meal_selection
                            UNION ALL
                            SELECT DATE_ADD(week_start_date, INTERVAL 4 DAY) AS selected_date, fri AS meal_label, shift_type FROM meal_selection
                            UNION ALL
                            SELECT DATE_ADD(week_start_date, INTERVAL 5 DAY) AS selected_date, sat AS meal_label, shift_type FROM meal_selection
                            UNION ALL
                            SELECT DATE_ADD(week_start_date, INTERVAL 6 DAY) AS selected_date, sun AS meal_label, shift_type FROM meal_selection
                        ) AS meals
                        $selectedWhere
                        GROUP BY DATE(selected_date), meal_label
                        ORDER BY DATE(selected_date), meal_label";

        $selectedMeals = [];
        if ($reportType === null || $reportType === 'selected_meals') {
            $selectedMealsStmt = $this->pdo->prepare($selectedSql);
            $selectedMealsStmt->execute($selectedParams);
            $selectedMeals = $selectedMealsStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return [
            'totals' => $totals,
            'topMeals' => $topMeals,
            'staff' => $staff,
            'servedMeals' => $servedMeals,
            'selectedMeals' => $selectedMeals,
        ];
    }
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
