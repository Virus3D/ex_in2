<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\FnsReceipt;
use App\Entity\FnsReceiptItem;
use App\Entity\ProductCategory;
use App\Entity\Spend;
use App\Repository\FnsReceiptRepository;
use App\Repository\SpendRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class ReceiptImporter
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CategoryDetector $categoryDetector,
        private readonly SpendRepository $spendRepository,
        private readonly LoggerInterface $logger,
    ) {
    }// end __construct()

    /**
     * Импортировать нормализованные данные чека из ФНС.
     *
     * @param array<string, mixed> $fiscalData Данные чека из normalizeFiscalData()
     * @param string|null          $receiptKey Уникальный ключ чека из ФНС
     * @param string|null          $kktOwner   Владелец ККТ
     */
    public function import(array $fiscalData, ?string $receiptKey = null, ?string $kktOwner = null): ?FnsReceipt
    {
        if (! isset($fiscalData['dateTime']) || ! isset($fiscalData['totalSum']) || $receiptKey === null) {
            $this->logger->warning('Неполные данные чека ФНС', ['receiptKey' => $receiptKey]);

            return null;
        }

        // Проверяем, не импортирован ли уже чек.
        $existingReceipt = $this->entityManager->getRepository(FnsReceipt::class)->findByKey($receiptKey);

        if ($existingReceipt !== null) {
            $this->logger->info('Чек уже импортирован', ['receiptKey' => $receiptKey]);

            return $existingReceipt;
        }

        // Парсим дату.
        $fiscalDate = $this->parseFiscalDate($fiscalData['dateTime']);

        if ($fiscalDate === null) {
            $this->logger->warning('Не удалось распарсить дату чека', ['dateTime' => $fiscalData['dateTime']]);

            return null;
        }

        // Создаём чек.
        $fnsReceipt = new FnsReceipt();
        $fnsReceipt->setReceiptKey($receiptKey);
        $fnsReceipt->setKktOwner($kktOwner);
        $fnsReceipt->setFiscalDate($fiscalDate);
        $fnsReceipt->setTotalSum((int) round((float) $fiscalData['totalSum'] * 100));

        // Создаём элементы чека и определяем категории.
        $totalItems = 0;
        $categorizedItems = 0;

        if (isset($fiscalData['items']) && is_array($fiscalData['items'])) {
            foreach ($fiscalData['items'] as $itemData) {
                $item = $this->createItem($fnsReceipt, $itemData);
                ++$totalItems;

                if ($item->getCategory() !== null) {
                    ++$categorizedItems;
                }
            }
        }

        // Сохраняем.
        $this->entityManager->persist($fnsReceipt);
        $this->entityManager->flush();

        $this->logger->info(
            'Чек ФНС импортирован',
            [
                'receiptKey'  => $receiptKey,
                'items'       => $totalItems,
                'categorized' => $categorizedItems,
                'totalSum'    => $fnsReceipt->getTotalSum(),
            ]
        );

        return $fnsReceipt;
    }// end import()

    /**
     * Создать элемент чека с определением категории.
     *
     * @param array<string, mixed> $itemData
     */
    private function createItem(FnsReceipt $receipt, array $itemData): FnsReceiptItem
    {
        $item = new FnsReceiptItem();
        $item->setName($itemData['name'] ?? '?');
        $item->setPrice((int) round((float) ($itemData['price'] ?? 0) * 100));
        $item->setQuantity((int) ($itemData['quantity'] ?? 1));
        $item->setSum((int) round((float) ($itemData['sum'] ?? 0) * 100));

        // Определяем категорию.
        /*
            $category = $this->categoryDetector->detect($item->getName());

            if ($category !== null) {
            $item->setCategory($category);
            }
        */

        $receipt->addItem($item);

        return $item;
    }// end createItem()

    /**
     * Распарсить дату из ФНС API.
     */
    private function parseFiscalDate(string $dateTime): ?DateTime
    {
        // Пробуем несколько форматов.
        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d\TH:i:s',
            'd.m.Y H:i:s',
            'd.m.Y H:i',
            'Y/m/d H:i:s',
        ];

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $dateTime);

            if ($date !== false) {
                return $date;
            }
        }

        // Пробуем strtotime как fallback.
        $timestamp = strtotime($dateTime);

        if ($timestamp !== false) {
            $date = new DateTime();
            $date->setTimestamp($timestamp);

            return $date;
        }

        return null;
    }// end parseFiscalDate()

    /**
     * Импортировать несколько чеков пакетно.
     *
     * @param array<int, array{data: array<string, mixed>, key?: string, kktOwner?: string}> $receiptsData
     *
     * @return array<int, FnsReceipt|null>
     */
    public function importBatch(array $receiptsData): array
    {
        $results = [];

        foreach ($receiptsData as $index => $receiptData) {
            try {
                $results[$index] = $this->import(
                    $receiptData['data'],
                    $receiptData['key'] ?? null,
                    $receiptData['kktOwner'] ?? null,
                );
            } catch (Throwable $e) {
                $this->logger->error(
                    'Ошибка импорта чека',
                    [
                        'index' => $index,
                        'error' => $e->getMessage(),
                    ]
                );
                $results[$index] = null;
            }
        }

        // Финальный flush после всех записей.
        $this->entityManager->flush();

        return $results;
    }// end importBatch()
}// end class
