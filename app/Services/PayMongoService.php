<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayMongoService
{
    public static function allowedMethods(): array
    {
        return config('paymongo.allowed_payment_methods', ['gcash', 'paymaya']);
    }

    public function createPaymentIntent(int $amountCentavos, string $description, string $currency = 'PHP'): array
    {
        return $this->request('POST', '/payment_intents', [
            'data' => [
                'attributes' => [
                    'amount'                 => $amountCentavos,
                    'currency'               => $currency,
                    'description'            => $description,
                    'payment_method_allowed' => self::allowedMethods(),
                    'capture_type'           => 'automatic',
                ],
            ],
        ]);
    }

    /** GCash or Maya (paymaya) — billing required for e-wallet checkout */
    public function createEwalletPaymentMethod(string $type, string $name, string $email, string $phone): array
    {
        if (! in_array($type, self::allowedMethods(), true)) {
            throw new \RuntimeException('Invalid e-wallet type');
        }

        return $this->request('POST', '/payment_methods', [
            'data' => [
                'attributes' => [
                    'type'    => $type,
                    'billing' => [
                        'name'  => $name,
                        'email' => $email,
                        'phone' => $phone,
                    ],
                ],
            ],
        ]);
    }

    public function getPaymentIntent(string $intentId): array
    {
        return $this->request('GET', "/payment_intents/{$intentId}");
    }

    public function attachPaymentMethod(string $intentId, string $paymentMethodId, string $returnUrl): array
    {
        return $this->request('POST', "/payment_intents/{$intentId}/attach", [
            'data' => [
                'attributes' => [
                    'payment_method' => $paymentMethodId,
                    'return_url'     => $returnUrl,
                ],
            ],
        ]);
    }

    private function request(string $method, string $endpoint, array $payload = []): array
    {
        $url = rtrim(config('paymongo.api_url'), '/') . $endpoint;
        $secret = config('paymongo.secret_key');

        if (empty($secret)) {
            throw new \RuntimeException('PayMongo secret key is not configured. Set PAYMONGO_SECRET_KEY in .env');
        }

        $auth = base64_encode($secret . ':');

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Basic ' . $auth,
        ])->asJson()->{strtolower($method)}($url, $method === 'GET' ? [] : $payload);

        $data = $response->json();

        if ($response->failed()) {
            $msg = $data['errors'][0]['detail'] ?? 'PayMongo API error';
            throw new \RuntimeException($msg, $response->status());
        }

        return $data;
    }
}
