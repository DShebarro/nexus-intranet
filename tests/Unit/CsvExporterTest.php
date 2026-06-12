<?php

namespace Tests\Unit;

use App\Core\CsvExporter;
use PHPUnit\Framework\TestCase;

class CsvExporterTest extends TestCase
{
    public function testGeneratesCsvWithHeaders(): void
    {
        $csv = CsvExporter::generate(
            ['Nome', 'Valor'],
            [['Item A', '100'], ['Item B', '200']]
        );

        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('Nome;Valor', $csv);
        $this->assertStringContainsString('Item A', $csv);
        $this->assertStringContainsString('100', $csv);
    }
}
