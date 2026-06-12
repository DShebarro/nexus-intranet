<?php
namespace App\Core;

class CsvExporter
{
    public static function generate(array $headers, array $rows): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers, ';');

        foreach ($rows as $row) {
            fputcsv($output, $row, ';');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return "\xEF\xBB\xBF" . $csv; // BOM UTF-8
    }
}
