<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Card;
use App\Helper\FilterDataHelper;
use App\Repository\SpendRepository;
use DateTime;
use Exception;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

use function count;

#[AsTwigComponent]
final class ExpensesChart
{
    public function __construct(
        private readonly SpendRepository $spendRepository,
        private readonly FilterDataHelper $filterDataHelper,
    ) {}//end __construct()

    /**
     * Получить данные для диаграммы по комментариям.
     *
     * @return array<string, mixed>
     */
    public function getCommentData(?Card $card = null): array
    {
        try {
            $expenses = $this->spendRepository->getExpensesByComment(
                $this->filterDataHelper->startDate,
                $this->filterDataHelper->endDate,
                $card
            );

            if (empty($expenses)) {
                return [
                    'labels' => ['Нет данных'],
                    'data'   => [0],
                    'colors' => ['#6c757d'],
                ];
            }

            $labels = array_keys($expenses);
            $data   = array_values($expenses);
            $colors = $this->generateColors(count($expenses));

            return [
                'labels' => $labels,
                'data'   => $data,
                'colors' => $colors,
            ];
        }
        catch (Exception $e) {
            return [
                'labels' => ['Ошибка загрузки'],
                'data'   => [0],
                'colors' => ['#dc3545'],
            ];
        }//end try
    }//end getCommentData()

    /**
     * Получить данные для диаграммы по дням.
     *
     * @return array<string, mixed>
     */
    public function getDailyData(?Card $card = null): array
    {
        try {
            $expenses = $this->spendRepository->getExpensesByDay(
                $this->filterDataHelper->startDate,
                $this->filterDataHelper->endDate,
                $card
            );

            if (empty($expenses)) {
                return [
                    'labels' => ['Нет данных'],
                    'data'   => [0],
                ];
            }

            $labels = array_keys($expenses);
            $data   = array_values($expenses);

            return [
                'labels' => $labels,
                'data'   => $data,
            ];
        }
        catch (Exception $e) {
            error_log('Error in getDailyData: '.$e->getMessage());

            return [
                'labels' => ['Ошибка загрузки'.$e->getMessage()],
                'data'   => [0],
            ];
        }//end try
    }//end getDailyData()

    /**
     * Получить общую сумму расходов.
     */
    public function getTotalExpenses(?Card $card = null): int
    {
        $expenses = $this->spendRepository->getExpensesByComment(
            $this->filterDataHelper->startDate,
            $this->filterDataHelper->endDate,
            $card
        );

        return array_sum($expenses) ?: 0;
    }//end getTotalExpenses()

    /**
     * Получить отформатированную общую сумму расходов.
     */
    public function getFormattedTotalExpenses(?Card $card = null): string
    {
        $total = $this->getTotalExpenses($card);

        return number_format($total / 100, 2, ',', ' ');
    }//end getFormattedTotalExpenses()

    /**
     * Генерировать цвета для диаграммы.
     *
     * @return string[]
     */
    private function generateColors(int $count): array
    {
        $colors = [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40',
            '#FF6384',
            '#C9CBCF',
            '#4BC0C0',
            '#FF6384',
        ];

        $result = [];
        for ($i = 0; $i < $count; ++$i) {
            $result[] = $colors[$i % count($colors)];
        }

        return $result;
    }//end generateColors()
}//end class
