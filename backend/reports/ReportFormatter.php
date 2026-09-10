<?php
declare(strict_types=1);

namespace Canteen\Reports;

require_once __DIR__ . '/ReportDataService.php';
require_once __DIR__ . '/../../config/app.php';

use DateTimeImmutable;
use DateTimeZone;

final class ReportFormatter
{
    public static function text(mixed $value): string
    {
        return htmlspecialchars($value === null || $value === '' ? '—' : (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function date(?string $value): string
    {
        return $value ? (new DateTimeImmutable($value, new DateTimeZone('Africa/Accra')))->format('d M Y') : '—';
    }

    public static function build(array $data, array $filters, array $user): array
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('Africa/Accra'));
        $start = $filters['start_date'];
        $end = $filters['end_date'];
        $periodType = 'Custom';
        $period = 'All dates';
        if ($start && $end) {
            $a = new DateTimeImmutable($start);
            $b = new DateTimeImmutable($end);
            $period = $a->format('d F Y') . ' - ' . $b->format('d F Y');
            if ($start === $end) {
                $periodType = 'Daily';
                $period = $a->format('d F Y');
            } elseif ($a->format('d') === '01' && $end === $a->format('Y-m-t')) {
                $periodType = 'Monthly';
                $period = $a->format('F Y');
            } elseif ($a->diff($b)->days === 6) {
                $periodType = 'Weekly';
            }
        } elseif ($start) {
            $period = 'From ' . self::date($start);
        } elseif ($end) {
            $period = 'Through ' . self::date($end);
        } else {
            $periodType = 'All Dates';
        }
        $name = trim(($user['name'] ?? '') . ' ' . ($user['lastname'] ?? ''));
        $sections = [];
        $definitions = [
            'meals_served' => ['servedMeals', 'Meals Served', ['Date', 'Meal', 'Served'], ['date', 'meal_label', 'count']],
            'selected_meals' => ['selectedMeals', 'Selected Meals', ['Date', 'Meal', 'Selected'], ['date', 'meal_label', 'count']],
            'daily_totals' => ['totals', 'Daily Totals', ['Date', 'Total Meals'], ['date', 'total']],
            'top_meals' => ['topMeals', 'Top Meals (up to 10)', ['Meal', 'Served'], ['meal_label', 'count']],
            'staff' => ['staff', 'Top Staff Served (up to 10)', ['Staff', 'Meals'], ['staff_name', 'count']],
        ];
        foreach ($definitions as $type => [$key, $title, $columns, $keys]) {
            if ($filters['report_type'] !== '' && $filters['report_type'] !== $type) continue;
            $rows = [];
            $total = 0;
            foreach ($data[$key] ?? [] as $row) {
                $cells = [];
                foreach ($keys as $field) {
                    $cells[] = match ($field) {
                        'date' => self::date($row['date'] ?? null),
                        'staff_name' => trim(($row['name'] ?? '') . ' ' . ($row['lastname'] ?? '')),
                        'count', 'total' => number_format((int) ($row[$field] ?? 0)),
                        default => $row[$field] ?? null,
                    };
                }
                $total += (int) ($row['count'] ?? $row['total'] ?? 0);
                $rows[] = $cells;
            }
            $sections[] = compact('title', 'columns', 'rows', 'total');
        }
        $summary = [];
        foreach ($sections as $section) {
            $summary[$section['title']] = number_format($section['total']);
        }
        $logo = self::logo();
        return [
            'title' => ReportDataService::TYPES[$filters['report_type']],
            'type' => $periodType . ' Report', 'period_label' => $period,
            'generated_at' => $now->format('d F Y, h:i:s A') . ' (Africa/Accra)',
            'generated_date' => $now->format('d F Y'), 'generated_time' => $now->format('h:i:s A') . ' (Africa/Accra)',
            'generated_by' => $name ?: ($user['username'] ?? 'System User'),
            'generated_by_role' => ($user['role'] ?? '') === 'admin' ? 'Administrator' : ($user['role'] ?? '—'),
            'reference' => 'CANT-' . $now->format('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3))),
            'filters' => $filters, 'summary' => $summary, 'sections' => $sections,
            'logo' => $logo,
            'filename' => 'Concentrix_Canteen_' . ($filters['report_type'] ?: 'Summary') . '_' . str_replace(' ', '_', $periodType) . '_Report_' . ($start ?: 'beginning') . '_to_' . ($end ?: 'latest') . '.pdf',
        ];
    }

    private static function logo(): ?array
    {
        $root = realpath(__DIR__ . '/../../frontend');
        $relative = \Canteen\Config\env('REPORT_COMPANY_LOGO', 'img/concentrix-logo.png');
        $path = realpath($root . '/' . $relative);
        if (!$path || !str_starts_with(strtolower($path), strtolower($root . DIRECTORY_SEPARATOR)) || !is_readable($path)) return null;
        $size = @getimagesize($path);
        if (!$size || !in_array($size['mime'], ['image/png', 'image/jpeg'], true)) return null;
        // Embed local bytes so a snapshot keeps its branding if the asset changes.
        $width = min(150, 42 * $size[0] / $size[1]);
        return ['src' => 'data:' . $size['mime'] . ';base64,' . base64_encode(file_get_contents($path)), 'width' => $width, 'height' => $width * $size[1] / $size[0]];
    }
}
