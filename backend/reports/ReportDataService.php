<?php
declare(strict_types=1);

namespace Canteen\Reports;

require_once __DIR__ . '/../models/MealSelectionModel.php';

use Canteen\Models\MealSelectionModel;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use PDO;

final class ReportDataService
{
    public const TYPES = ['' => 'Meal Service Summary', 'meals_served' => 'Meals Served', 'selected_meals' => 'Selected Meals', 'daily_totals' => 'Daily Totals', 'top_meals' => 'Top Meals'];

    public static function validate(array $input): array
    {
        $filters = [];
        foreach (['start_date', 'end_date', 'shift_type', 'report_type'] as $key) {
            if (isset($input[$key]) && !is_string($input[$key])) {
                throw new InvalidArgumentException('Invalid report filters.');
            }
            $filters[$key] = trim($input[$key] ?? '');
        }
        foreach (['start_date', 'end_date'] as $key) {
            $value = $filters[$key];
            $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value, new DateTimeZone('Africa/Accra'));
            if ($value !== '' && (!$date || $date->format('Y-m-d') !== $value || $value < '1000-01-01')) {
                throw new InvalidArgumentException('Please enter valid report dates.');
            }
        }
        if ($filters['start_date'] && $filters['end_date'] && $filters['start_date'] > $filters['end_date']) {
            throw new InvalidArgumentException('The end date must be on or after the start date.');
        }
        if (!in_array($filters['shift_type'], ['', 'Day', 'Night'], true) || !array_key_exists($filters['report_type'], self::TYPES)) {
            throw new InvalidArgumentException('Please select a valid report type and shift.');
        }
        return $filters;
    }

    public static function fetch(PDO $pdo, array $filters): array
    {
        // Keep the established queries, aggregation semantics and ranking limits.
        return (new MealSelectionModel($pdo))->getSummary($filters, $filters['report_type'] ?: null);
    }
}
