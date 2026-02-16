<?php

namespace App\Imports;

use App\Events\ImportProgressUpdated;
use App\Models\Categoria;
use App\Models\Cluster;
use App\Models\Embalagem;
use App\Models\Filial;
use App\Models\ImportBatch;
use App\Models\TipoMarca;
use App\Models\TipoPessoa;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class GenericImport implements OnEachRow, WithChunkReading, WithHeadingRow, WithCustomCsvSettings
{
    private string $batchId;
    private string $userId;
    private string $type;
    private int $totalRows;
    private int $processedRows = 0;
    private int $errorCount = 0;
    private int $updateEvery;

    public function __construct(string $batchId, string $userId, string $type, int $totalRows, int $updateEvery = 10)
    {
        $this->batchId = $batchId;
        $this->userId = $userId;
        $this->type = $type;
        $this->totalRows = $totalRows;
        $this->updateEvery = $updateEvery;
    }

    public function onRow(Row $row): void
    {
        $rowIndex = $row->getIndex();
        $data = $this->normalizeKeys($row->toArray());

        $codigo = Arr::get($data, 'codigo');
        $descricao = Arr::get($data, 'descricao');

        if ($codigo === null || $descricao === null) {
            $this->processedRows++;
            $this->updateProgress($rowIndex, 'Skipped row due to missing codigo or descricao');
            return;
        }

        try {
            $this->resolveModel()::updateOrCreate(
                ['codigo' => $codigo],
                [
                    'descricao' => $descricao,
                    'usuario_responsavel_id' => $this->userId,
                ]
            );
            $this->processedRows++;
            $this->updateProgress($rowIndex, 'Imported codigo ' . $codigo);
        } catch (\Throwable $exception) {
            $this->processedRows++;
            $this->errorCount++;
            $this->updateProgress($rowIndex, 'Error importing codigo ' . $codigo);
            Log::warning('Import row failed', [
                'batch_id' => $this->batchId,
                'row' => $rowIndex,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
        ];
    }

    private function resolveModel(): string
    {
        return match ($this->type) {
            'filiais' => Filial::class,
            'tipos_pessoa' => TipoPessoa::class,
            'tipos_marca' => TipoMarca::class,
            'embalagens' => Embalagem::class,
            'clusters' => Cluster::class,
            'categorias' => Categoria::class,
            default => Filial::class,
        };
    }

    private function normalizeKeys(array $data): array
    {
        $normalized = [];

        foreach ($data as $key => $value) {
            $cleanKey = ltrim((string) $key, "\xEF\xBB\xBF");
            $cleanKey = Str::slug(trim($cleanKey), '_');
            $normalized[$cleanKey] = $value;
        }

        return $normalized;
    }

    private function updateProgress(int $rowIndex, string $log): void
    {
        if ($this->processedRows % $this->updateEvery !== 0 && $this->processedRows !== $this->totalRows) {
            return;
        }

        $percentage = 0;
        if ($this->totalRows > 0) {
            $percentage = (int) floor(($this->processedRows / $this->totalRows) * 100);
            if ($percentage > 100) {
                $percentage = 100;
            }
        }

        ImportBatch::query()
            ->where('id', $this->batchId)
            ->update([
                'processed_rows' => $this->processedRows,
                'percentage' => $percentage,
                'last_log' => $log,
                'current_step' => 'row ' . $rowIndex,
            ]);

        $batch = ImportBatch::query()->find($this->batchId);
        if ($batch) {
            event(new ImportProgressUpdated($batch));
        }
    }
}
