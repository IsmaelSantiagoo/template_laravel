<?php

namespace App\Imports;

use App\Events\ImportProgressUpdated;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Cluster;
use App\Models\Embalagem;
use App\Models\Filial;
use App\Models\ImportBatch;
use App\Models\Motorista;
use App\Models\NotaFiscal;
use App\Models\Produto;
use App\Models\ProdutoNotaFiscal;
use App\Models\TipoMarca;
use App\Models\TipoPessoa;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
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

        try {
            $this->importRow($data, $rowIndex);
            $this->processedRows++;
            $identifier = $this->getRowIdentifier($data);
            $this->updateProgress($rowIndex, 'Imported ' . $identifier);
        } catch (\Throwable $exception) {
            $this->processedRows++;
            $this->errorCount++;
            $identifier = $this->getRowIdentifier($data);
            $this->updateProgress($rowIndex, 'Error: ' . $identifier . ' — ' . Str::limit($exception->getMessage(), 80));
            Log::warning('Import row failed', [
                'batch_id' => $this->batchId,
                'row' => $rowIndex,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function importRow(array $data, int $rowIndex): void
    {
        match ($this->type) {
            'filiais', 'tipos_pessoa', 'tipos_marca', 'embalagens', 'clusters', 'categorias'
            => $this->importLookup($data),
            'clientes' => $this->importCliente($data),
            'produtos' => $this->importProduto($data),
            'motoristas' => $this->importMotorista($data),
            'notas_fiscais' => $this->importNotaFiscal($data),
            'produtos_nf' => $this->importProdutoNf($data),
            default => throw new \RuntimeException("Tipo de importação desconhecido: {$this->type}"),
        };
    }

    // ─── Lookup (codigo + descricao) ────────────────────────────────────

    private function importLookup(array $data): void
    {
        $codigo = Arr::get($data, 'codigo');
        $descricao = Arr::get($data, 'descricao');

        if ($codigo === null || $descricao === null) {
            throw new \RuntimeException('Missing codigo or descricao');
        }

        $this->resolveModel()::updateOrCreate(
            ['codigo' => $codigo],
            [
                'descricao' => $descricao,
                'usuario_responsavel_id' => $this->userId,
            ]
        );
    }

    // ─── Clientes ───────────────────────────────────────────────────────

    private function importCliente(array $data): void
    {
        $codigo = Arr::get($data, 'codigo');
        $nomeFantasia = Arr::get($data, 'nome_fantasia');

        if (blank($codigo) || blank($nomeFantasia)) {
            throw new \RuntimeException('Missing codigo or nome_fantasia');
        }

        $categoriaId = $this->resolveFk(Categoria::class, 'codigo', Arr::get($data, 'categoria_codigo'));
        $tipoPessoaId = $this->resolveFk(TipoPessoa::class, 'codigo', Arr::get($data, 'tipo_pessoa_codigo'));

        Cliente::updateOrCreate(
            ['codigo' => $codigo],
            [
                'documento' => Arr::get($data, 'documento'),
                'nome_fantasia' => $nomeFantasia,
                'razao_social' => Arr::get($data, 'razao_social'),
                'endereco' => Arr::get($data, 'endereco'),
                'complemento' => Arr::get($data, 'complemento'),
                'bairro' => Arr::get($data, 'bairro'),
                'cidade' => Arr::get($data, 'cidade'),
                'uf' => Arr::get($data, 'uf'),
                'cep' => Arr::get($data, 'cep'),
                'latitude' => $this->toDecimal(Arr::get($data, 'latitude')),
                'longitude' => $this->toDecimal(Arr::get($data, 'longitude')),
                'categoria_id' => $categoriaId,
                'tipo_pessoa_id' => $tipoPessoaId,
                'pdv_ativo' => $this->toBool(Arr::get($data, 'pdv_ativo'), true),
                'telefone' => Arr::get($data, 'telefone'),
                'telefone_principal' => $this->toBool(Arr::get($data, 'telefone_principal'), false),
                'usuario_responsavel_id' => $this->userId,
            ]
        );
    }

    // ─── Produtos ───────────────────────────────────────────────────────

    private function importProduto(array $data): void
    {
        $codigo = Arr::get($data, 'codigo');
        $nome = Arr::get($data, 'nome');

        if (blank($codigo) || blank($nome)) {
            throw new \RuntimeException('Missing codigo or nome');
        }

        $tipoMarcaId = $this->resolveFk(TipoMarca::class, 'codigo', Arr::get($data, 'tipo_marca_codigo'));
        $embalagemId = $this->resolveFk(Embalagem::class, 'codigo', Arr::get($data, 'embalagem_codigo'));

        Produto::updateOrCreate(
            ['codigo' => $codigo],
            [
                'nome' => $nome,
                'descricao' => Arr::get($data, 'descricao'),
                'quantidade' => $this->toInt(Arr::get($data, 'quantidade')),
                'tipo_marca_id' => $tipoMarcaId,
                'embalagem_id' => $embalagemId,
                'ean' => Arr::get($data, 'ean'),
                'usuario_responsavel_id' => $this->userId,
            ]
        );
    }

    // ─── Motoristas ─────────────────────────────────────────────────────

    private function importMotorista(array $data): void
    {
        $codigo = Arr::get($data, 'codigo');
        $nome = Arr::get($data, 'nome');

        if (blank($codigo) || blank($nome)) {
            throw new \RuntimeException('Missing codigo or nome');
        }

        $filialId = $this->resolveFk(Filial::class, 'codigo', Arr::get($data, 'filial_codigo'));
        $clusterId = $this->resolveFk(Cluster::class, 'codigo', Arr::get($data, 'cluster_codigo'));

        $payload = [
            'nome' => $nome,
            'cpf' => Arr::get($data, 'cpf'),
            'status' => Arr::get($data, 'status', 'ativo') ?: 'ativo',
            'celular_corporativo' => Arr::get($data, 'celular_corporativo'),
            'data_admissao' => $this->toDate(Arr::get($data, 'data_admissao')),
            'filial_id' => $filialId,
            'cluster_id' => $clusterId,
            'usuario_responsavel_id' => $this->userId,
        ];

        $senha = Arr::get($data, 'senha');
        if (!blank($senha)) {
            $payload['senha'] = Hash::make($senha);
        }

        Motorista::updateOrCreate(['codigo' => $codigo], $payload);
    }

    // ─── Notas Fiscais ──────────────────────────────────────────────────

    private function importNotaFiscal(array $data): void
    {
        $numero = Arr::get($data, 'numero');

        if (blank($numero)) {
            throw new \RuntimeException('Missing numero');
        }

        $clienteId = $this->resolveFk(Cliente::class, 'codigo', Arr::get($data, 'cliente_codigo'));

        NotaFiscal::updateOrCreate(
            ['numero' => $numero],
            [
                'pedido' => Arr::get($data, 'pedido'),
                'mapa' => Arr::get($data, 'mapa'),
                'cliente_id' => $clienteId,
                'rota_nome' => Arr::get($data, 'rota_nome'),
                'data_operacao' => $this->toDate(Arr::get($data, 'data_operacao')),
                'data_emissao' => $this->toDate(Arr::get($data, 'data_emissao')),
                'valor_bruto' => $this->toDecimal(Arr::get($data, 'valor_bruto')) ?? 0,
                'total_desconto' => $this->toDecimal(Arr::get($data, 'total_desconto')) ?? 0,
                'valor_total' => $this->toDecimal(Arr::get($data, 'valor_total')) ?? 0,
                'status' => Arr::get($data, 'status', 'ativa') ?: 'ativa',
                'usuario_responsavel_id' => $this->userId,
            ]
        );
    }

    // ─── Produtos da NF ─────────────────────────────────────────────────

    private function importProdutoNf(array $data): void
    {
        $nfNumero = Arr::get($data, 'nf_numero');
        $produtoCodigo = Arr::get($data, 'produto_codigo');

        if (blank($nfNumero) || blank($produtoCodigo)) {
            throw new \RuntimeException('Missing nf_numero or produto_codigo');
        }

        $nf = NotaFiscal::query()->where('numero', $nfNumero)->first();
        if (!$nf) {
            throw new \RuntimeException("Nota fiscal '{$nfNumero}' não encontrada");
        }

        $produto = Produto::query()->where('codigo', $produtoCodigo)->first();
        if (!$produto) {
            throw new \RuntimeException("Produto '{$produtoCodigo}' não encontrado");
        }

        ProdutoNotaFiscal::updateOrCreate(
            [
                'nota_fiscal_id' => $nf->id,
                'produto_id' => $produto->id,
            ],
            [
                'quantidade' => $this->toInt(Arr::get($data, 'quantidade')) ?? 0,
                'usuario_responsavel_id' => $this->userId,
            ]
        );
    }

    // ─── Helpers ────────────────────────────────────────────────────────

    private function getRowIdentifier(array $data): string
    {
        return (string) (
            Arr::get($data, 'codigo')
            ?? Arr::get($data, 'numero')
            ?? Arr::get($data, 'nf_numero')
            ?? 'row'
        );
    }

    /**
     * Resolve a foreign key by looking up a related model by a unique column value.
     * Returns the model's id or null if the value is blank or not found.
     */
    private function resolveFk(string $modelClass, string $column, mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $record = $modelClass::query()->where($column, $value)->first();

        return $record?->id;
    }

    private function toBool(mixed $value, bool $default = false): bool
    {
        if (blank($value)) {
            return $default;
        }

        $v = strtolower(trim((string) $value));

        return in_array($v, ['s', 'sim', 'yes', 'y', '1', 'true'], true);
    }

    private function toDecimal(mixed $value): ?float
    {
        if (blank($value)) {
            return null;
        }

        $v = str_replace(',', '.', trim((string) $value));

        return is_numeric($v) ? (float) $v : null;
    }

    private function toInt(mixed $value): ?int
    {
        if (blank($value)) {
            return null;
        }

        $v = trim((string) $value);

        return is_numeric($v) ? (int) $v : null;
    }

    private function toDate(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $v = trim((string) $value);

        // Excel serial date number
        if (is_numeric($v) && (float) $v > 10000) {
            $timestamp = ((float) $v - 25569) * 86400;
            return date('Y-m-d', (int) $timestamp);
        }

        // Try parsing common formats
        foreach (['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y'] as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $v);
            if ($dt !== false) {
                return $dt->format('Y-m-d');
            }
        }

        return $v;
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
