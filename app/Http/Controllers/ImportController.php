<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessImportJob;
use App\Models\ImportBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    private const ALLOWED_TYPES = [
        'filiais',
        'tipos_pessoa',
        'tipos_marca',
        'embalagens',
        'clusters',
        'categorias',
    ];

    public function start(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:' . implode(',', self::ALLOWED_TYPES)],
            'file' => ['required', 'file', 'mimes:csv,xlsx,xls'],
        ], [
            'type.required' => 'Type is required.',
            'type.in' => 'Type is invalid.',
            'file.required' => 'File is required.',
            'file.mimes' => 'File must be CSV or Excel.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $path = $request->file('file')->store('imports');

        $batch = ImportBatch::query()->create([
            'user_id' => $user->id,
            'type' => $request->input('type'),
            'status' => 'pending',
            'total_rows' => 0,
            'processed_rows' => 0,
            'percentage' => 0,
            'last_log' => 'Queued',
            'current_step' => 'queued',
        ]);

        ProcessImportJob::dispatch($batch->id, $path, $batch->type, $user->id)
            ->onQueue('imports');

        return response()->json([
            'success' => true,
            'data' => $batch,
        ]);
    }

    public function list(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $imports = ImportBatch::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing', 'failed'])
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $imports,
        ]);
    }
}
