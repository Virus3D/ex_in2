<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Client\MoiChekiClient;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Log\LoggerInterface;

final class ReceiptExporter
{
    public function __construct(
        private readonly MoiChekiClient $client,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger,
    ) {
    }// end __construct()

    /**
     * Экспортирует чеки за период.
     *
     * @return array{receipts: array<int, array{name_short: string, id: string, fiscalData: array<string, mixed>}>}
     */
    public function export(string $dateFrom, string $dateTo): array
    {
        $receipts = $this->client->getReceipts($dateFrom, $dateTo);
        $this->logger->info(sprintf('Получено %d чеков за период %s – %s', count($receipts), $dateFrom, $dateTo));

        $transformedReceipts = [];
        $qrCodes = [];

        foreach ($receipts as $key => $receipt) {
            $transformed = $this->processReceipt($receipt['key']);
            $transformed['id'] = $receipt['key'] ?? '';
            $transformed['name_short'] = $receipt['kktOwner'] ?? '';
            $receipts[$key] = $transformed;
        }

        return ['receipts' => $receipts];
    }// end export()

    /**
     * Обрабатывает один чек: получает фискальные данные, нормализует их и оборачивает в итоговый формат.
     * Результат кэшируется на 24 часа.
     *
     * @param string $key Ключ чека
     *
     * @return array{id: string, createdAt: string, fiscalData: array<string, mixed>}
     */
    private function processReceipt(string $key): array
    {
        // Пытаемся взять из кэша; если нет — запрашиваем API и сохраняем на 24 часа.
        return $this->cache->get(
            'moi_cheki_receipt_' . md5($key),
            function (ItemInterface $item) use ($key) {
                $item->expiresAfter(86400);
                $this->logger->debug('Запрос фискальных данных для чека ' . $key);
                $fiscalData = $this->client->getFiscalData($key);

                // Нормализация сумм (аналог RoundToFloat64 в Go-версии).
                $normalized = $this->normalizeFiscalData($fiscalData);

                return $normalized;
            }
        );
    }// end processReceipt()

    /**
     * Нормализует фискальные данные чека: извлекает нужные поля и преобразует элементы.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function normalizeFiscalData(array $data): array
    {
        $normalData = [
            'user'     => $data['user'],
            'totalSum' => $data['totalSum'],
            'dateTime' => $data['dateTime'],
            'items'    => [],
        ];

        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $normalData['items'][] = [
                    'name'     => $item['name'] ?? '?',
                    'price'    => $item['price'] ?? 0,
                    'quantity' => $item['quantity'] ?? 1,
                    'sum'      => $item['sum'] ?? 0,
                ];
            }
        }

        return $normalData;
    }// end normalizeFiscalData()
}// end class
