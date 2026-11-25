<?php

declare(strict_types=1);

namespace App\Service\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Psr\Log\LoggerInterface;

final class MoiChekiClient
{
    private ?string $accessToken = null;

    private ?\DateTimeImmutable $tokenExpiresAt = null;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly string $apiUrl,
        private string $refreshToken,
        private readonly string $deviceId,
        private readonly string $userAgent,
    ) {
    }// end __construct()

    /**
     * Возвращает список чеков за период (ключи чеков).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getReceipts(string $dateFrom, string $dateTo): array
    {
        $this->ensureAccessToken();

        $payload = [
            'limit'    => 10000,
            'offset'   => 0,
            'dateFrom' => $dateFrom,
            'dateTo'   => $dateTo,
            'orderBy'  => 'CREATED_DATE:ASC',
        ];

        $response = $this->httpClient->request(
            'POST',
            $this->apiUrl . '/api/v1/receipt',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json'    => $payload,
            ]
        );

        $status = $response->getStatusCode();

        if ($status !== 200) {
            $body = $response->getContent(false);
            throw new \RuntimeException(sprintf('API вернул %d: %s', $status, $body));
        }

        $data = $response->toArray();

        return $data['receipts'] ?? [];
    }// end getReceipts()

    /**
     * Возвращает фискальные данные по конкретному чеку (по ключу).
     *
     * @return array<string, mixed>
     */
    public function getFiscalData(string $receiptKey): array
    {
        $this->ensureAccessToken();

        $response = $this->httpClient->request(
            'POST',
            $this->apiUrl . '/api/v1/receipt/fiscal_data',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json'    => ['key' => $receiptKey],
            ]
        );

        $status = $response->getStatusCode();

        if ($status !== 200) {
            $body = $response->getContent(false);
            throw new \RuntimeException(sprintf('Не удалось получить фискальные данные для %s (HTTP %d): %s', $receiptKey, $status, $body));
        }

        return $response->toArray();
    }// end getFiscalData()

    /**
     * Обновляет access-токен, если он пуст или истёк.
     */
    private function ensureAccessToken(): void
    {
        if ($this->accessToken !== null && $this->tokenExpiresAt !== null && new \DateTimeImmutable() < $this->tokenExpiresAt) {
            return;
        }

        $this->logger->info('Обновление access-токена МЧО...');

        $payload = [
            'refreshToken' => $this->refreshToken,
            'deviceInfo'   => [
                'sourceDeviceId' => $this->deviceId,
                'sourceType'     => 'WEB',
                'appVersion'     => '1.0.0',
                'metaDetails'    => ['userAgent' => $this->userAgent],
            ],
        ];

        $response = $this->httpClient->request(
            'POST',
            $this->apiUrl . '/api/v1/auth/token',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json'    => $payload,
            ]
        );

        $status = $response->getStatusCode();

        if ($status !== 200) {
            $body = $response->getContent(false);
            throw new \RuntimeException(sprintf('Ошибка аутентификации (HTTP %d): %s', $status, $body));
        }

        $data = $response->toArray();

        $this->accessToken   = $data['token'] ?? throw new \RuntimeException('В ответе отсутствует token');
        $this->refreshToken  = $data['refreshToken'] ?? $this->refreshToken;
        $expiresIn           = $data['tokenExpireIn'] ?? null;

        $this->tokenExpiresAt = $expiresIn !== null
            ? new \DateTimeImmutable($expiresIn)
            : new \DateTimeImmutable('+1 hour');

        $this->logger->info('Access-токен получен, действует до ' . $this->tokenExpiresAt->format('c'));
    }// end ensureAccessToken()
}// end class
