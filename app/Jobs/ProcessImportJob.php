<?php

namespace App\Jobs;

use App\Events\ImportProgressUpdated;
use App\Imports\CountRowsImport;
use App\Imports\GenericImport;
use App\Models\ImportBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $batchId;
    private string $path;
    private string $type;
    private int $userId;

    public function __construct(string $batchId, string $path, string $type, int $userId)
    {
        $this->batchId = $batchId;
        $this->path = $path;
        $this->type = $type;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        $batch = ImportBatch::query()->find($this->batchId);
        if (!$batch) {
            return;
        }

        $batch->update([
            'status' => 'processing',
            'current_step' => 'counting',
            'last_log' => 'Counting rows',
        ]);
        event(new ImportProgressUpdated($batch));

        $fullPath = Storage::path($this->path);

        try {
            $countImport = new CountRowsImport();
            Excel::import($countImport, $fullPath);

            $totalRows = $countImport->getRowCount();
            $batch->update([
                'total_rows' => $totalRows,
                'processed_rows' => 0,
                'percentage' => 0,
                'current_step' => 'importing',
                'last_log' => 'Starting import',
            ]);
            event(new ImportProgressUpdated($batch->fresh()));

            $import = new GenericImport($this->batchId, $this->userId, $this->type, $totalRows, 10);
            Excel::import($import, $fullPath);

            $batch->update([
                'status' => 'completed',
                'processed_rows' => $totalRows,
                'percentage' => 100,
                'current_step' => 'done',
                'last_log' => 'Import completed',
            ]);
            event(new ImportProgressUpdated($batch->fresh()));
        } catch (\Throwable $exception) {
            $batch->update([
                'status' => 'failed',
                'current_step' => 'failed',
                'last_log' => 'Import failed: ' . $exception->getMessage(),
            ]);
            event(new ImportProgressUpdated($batch->fresh()));
        }
    }
}
