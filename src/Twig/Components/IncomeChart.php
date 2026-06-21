<?php

/**
 * Expenses/Income.
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Card;
use App\Helper\FilterDataHelper;
use App\Repository\ReceiptRepository;
use Exception;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

use function count;

/**
 * Twig компонент для отображения диаграмм поступлений.
 */
#[AsTwigComponent]
final class IncomeChart
{
    public function __construct(
        private readonly ReceiptRepository $receiptRepository,
        private readonly FilterDataHelper $filterDataHelper,
    ) {
    }// end __construct()

    /**
     * Получить данные для диаграммы по комментариям.
     *
     * @return array<string, mixed>
     */
    public function getCommentData(?Card $card = null): array
    {
        try {
            $receipts = $this->receiptRepository->getReceiptsByComment(
                $this->filterDataHelper->startDate,
                $this->filterDataHelper->endDate,
                $card
            );

            if (empty($receipts)) {
                return [
                    'labels' => ['Нет данных'],
                    'data'   => [0],
                    'colors' => ['#6c757d'],
                ];
            }

            $labels = array_keys($receipts);
            $data   = array_values($receipts);
            $colors = $this->generateColors(count($receipts));

            return [
                'labels' => $labels,
                'data'   => $data,
                'colors' => $colors,
            ];
        } catch (Exception $e) {
            return [
                'labels' => ['Ошибка загрузки'],
                'data'   => [0],
                'colors' => ['#dc3545'],
            ];
        }// end try
    }// end getCommentData()

    /**
     * Получить данные для диаграммы по дням.
     *
     * @return array<string, mixed>
     */
    public function getDailyData(?Card $card = null): array
    {
        try {
            $receipts = $this->receiptRepository->getReceiptsByDay(
                $this->filterDataHelper->startDate,
                $this->filterDataHelper->endDate,
                $card
            );

            if (empty($receipts)) {
                return [
                    'labels' => ['Нет данных'],
                    'data'   => [0],
                ];
            }

            $labels = array_keys($receipts);
            $data   = array_values($receipts);

            return [
                'labels' => $labels,
                'data'   => $data,
            ];
        } catch (Exception $e) {
            error_log('Error in getDailyData: ' . $e->getMessage());

            return [
                'labels' => ['Ошибка загрузки' . $e->getMessage()],
                'data'   => [0],
            ];
        }// end try
    }// end getDailyData()

    /**
     * Получить общую сумму поступлений.
     */
    public function getTotalReceipts(?Card $card = null): int
    {
        $receipts = $this->receiptRepository->getReceiptsByComment(
            $this->filterDataHelper->startDate,
            $this->filterDataHelper->endDate,
            $card
        );

        return array_sum($receipts) ?: 0;
    }// end getTotalReceipts()

    /**
     * Получить отформатированную общую сумму поступлений.
     */
    public function getFormattedTotalReceipts(?Card $card = null): string
    {
        $total = $this->getTotalReceipts($card);

        return number_format($total / 100, 2, ',', ' ');
    }// end getFormattedTotalReceipts()

    /**
     * Генерировать цвета для диаграммы.
     *
     * @return string[]
     */
    private function generateColors(int $count): array
    {
        $colors = [
            '#28a745',
            '#20c997',
            '#17a2b8',
            '#6f42c1',
            '#fd7e14',
            '#ffc107',
            '#28a745',
            '#6c757d',
            '#17a2b8',
            '#28a745',
        ];

        $result = [];
        for ($i = 0; $i < $count; ++$i) {
            $result[] = $colors[$i % count($colors)];
        }

        return $result;
    }// end generateColors()
}// end class