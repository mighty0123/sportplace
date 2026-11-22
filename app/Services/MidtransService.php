<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $snapApiUrl;
    protected string $snapUrl;
    protected string $apiUrl;

    public function __construct()
    {
        $this->serverKey = (string) config('midtrans.server_key', '');
        $this->clientKey = (string) config('midtrans.client_key', '');
        $this->isProduction = (bool) config('midtrans.is_production', false);
        $this->snapApiUrl = (string) config('midtrans.snap_api_url', 'https://app.sandbox.midtrans.com/snap/v1/transactions');
        $this->snapUrl = (string) config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js');
        $this->apiUrl = (string) config('midtrans.api_url', 'https://api.sandbox.midtrans.com');
    }

    /**
     * Memeriksa apakah kredensial Midtrans sudah terisi di .env / konfigurasi.
     */
    public function isConfigured(): bool
    {
        return !empty($this->serverKey) && !empty($this->clientKey);
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    public function getSnapUrl(): string
    {
        return $this->snapUrl;
    }

    public function isProduction(): bool
    {
        return $this->isProduction;
    }

    /**
     * Buat Snap Transaction dan kembalikan token Snap untuk memicu popup Snapbox.
     *
     * @param array $payload Parameter transaksi (transaction_details, customer_details, item_details, dll)
     * @return array Array berisi 'token' dan 'redirect_url'
     * @throws Exception
     */
    public function createTransaction(array $payload): array
    {
        if (!$this->isConfigured()) {
            throw new Exception('Kunci Midtrans (Server Key / Client Key) belum dikonfigurasi di file .env');
        }

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->acceptJson()
                ->asJson()
                ->timeout(15)
                ->post($this->snapApiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'token'        => $data['token'] ?? null,
                    'redirect_url' => $data['redirect_url'] ?? null,
                ];
            }

            Log::error('Midtrans Snap Transaction Error', [
                'status'   => $response->status(),
                'response' => $response->body(),
                'payload'  => $payload,
            ]);

            $errorMsg = $response->json('error_messages.0') ?? $response->body();
            throw new Exception("Gagal membuat transaksi Midtrans: {$errorMsg}");
        } catch (Exception $e) {
            Log::error('Midtrans Service Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Helper cepat untuk mendapatkan Snap Token saja.
     */
    public function createSnapToken(array $payload): ?string
    {
        $res = $this->createTransaction($payload);
        return $res['token'] ?? null;
    }

    /**
     * Verifikasi keaslian webhook notification dari Midtrans menggunakan SHA512 signature.
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
        return hash_equals($expectedSignature, $signatureKey);
    }

    /**
     * Cek status transaksi langsung ke server Midtrans berdasarkan order_id.
     */
    public function getStatus(string $orderId): ?array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->acceptJson()
                ->timeout(10)
                ->get("{$this->apiUrl}/v2/{$orderId}/status");

            return $response->successful() ? $response->json() : null;
        } catch (Exception $e) {
            Log::error("Gagal memeriksa status Midtrans untuk order {$orderId}: " . $e->getMessage());
            return null;
        }
    }
}

