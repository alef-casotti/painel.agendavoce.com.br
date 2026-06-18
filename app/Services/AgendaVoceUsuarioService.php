<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AgendaVoceUsuarioService
{
    /**
     * @return array{data: ?array, error: ?string}
     */
    public function fetchStats(): array
    {
        $baseUrl = rtrim(config('services.usuarios.base_url') ?? '', '/');
        $apiToken = config('services.usuarios.api_token');

        if (! $baseUrl || ! $apiToken) {
            return [
                'data' => null,
                'error' => 'Configuração da API de usuários não encontrada. Verifique as variáveis de ambiente.',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->acceptJson()
                ->get("{$baseUrl}/api/usuarios/stats");

            if ($response->successful()) {
                return [
                    'data' => $response->json('data'),
                    'error' => null,
                ];
            }

            Log::warning('Usuario stats API returned an error response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return [
                'data' => null,
                'error' => 'Não foi possível carregar as estatísticas de clientes no momento.',
            ];
        } catch (\Throwable $exception) {
            Log::error('Usuario stats API request failed', [
                'message' => $exception->getMessage(),
            ]);

            return [
                'data' => null,
                'error' => 'Erro ao conectar com a API de estatísticas de clientes.',
            ];
        }
    }

    /**
     * @return array{items: Collection<int, array<string, mixed>>, error: ?string}
     */
    public function fetchAssinaturasAtivas(): array
    {
        $baseUrl = rtrim(config('services.usuarios.base_url') ?? '', '/');
        $apiToken = config('services.usuarios.api_token');

        if (! $baseUrl || ! $apiToken) {
            return [
                'items' => collect(),
                'error' => 'Configuração da API de usuários não encontrada. Verifique as variáveis de ambiente.',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->acceptJson()
                ->get("{$baseUrl}/api/usuarios");

            if (! $response->successful()) {
                Log::warning('Usuario list API returned an error response', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return [
                    'items' => collect(),
                    'error' => 'Não foi possível carregar os recebimentos no momento.',
                ];
            }

            $items = collect($response->json('data', []))
                ->filter(function (array $usuario) {
                    $assinatura = $usuario['assinatura_ativa'] ?? null;

                    return ! empty($assinatura)
                        && strtolower((string) ($assinatura['status'] ?? '')) === 'active';
                })
                ->map(function (array $usuario) {
                    $assinatura = $usuario['assinatura_ativa'];
                    $startedAt = ! empty($assinatura['started_at'])
                        ? Carbon::parse($assinatura['started_at'])
                        : null;

                    return [
                        'id' => $usuario['id'],
                        'nome' => $usuario['nome'] ?? '-',
                        'email' => $usuario['email'] ?? '-',
                        'plano' => strtoupper((string) ($assinatura['plan'] ?? $usuario['plano'] ?? '-')),
                        'valor' => (float) ($assinatura['price'] ?? 0),
                        'status' => $assinatura['status'] ?? 'active',
                        'started_at' => $startedAt,
                        'started_at_formatada' => $startedAt?->format('d/m/Y'),
                    ];
                })
                ->sortBy('nome', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            return [
                'items' => $items,
                'error' => null,
            ];
        } catch (\Throwable $exception) {
            Log::error('Usuario list API request failed', [
                'message' => $exception->getMessage(),
            ]);

            return [
                'items' => collect(),
                'error' => 'Erro ao conectar com a API de recebimentos.',
            ];
        }
    }
}
