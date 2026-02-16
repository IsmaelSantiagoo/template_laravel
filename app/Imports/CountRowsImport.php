<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class CountRowsImport implements OnEachRow, WithChunkReading, WithHeadingRow, WithCustomCsvSettings
{
    private int $rowCount = 0;

    public function onRow(Row $row): void
    {
        $this->rowCount++;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
        ];
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
