<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Twig;

use Money\Currency;
use Money\Money;
use NumberFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class AppExtension extends AbstractExtension
{
    /**
     * @return array<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('formatCurrency', [$this, 'formatCurrency']),
            new TwigFilter('group_by', [$this, 'groupBy']),
        ];
    }//end getFilters()

    public function formatCurrency(int $amount, string $currency = 'RUB', string $locale = 'ru_RU'): string|false
    {
        // Создаем объект Money, где сумма переведена из копеек в рубли
        $money = new Money($amount, new Currency($currency));

        // Настраиваем форматирование
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        return $formatter->format($money->getAmount() / 100);
    }//end formatCurrency()

    public function groupBy(array $items, string $key): array
    {
        $result = [];

        foreach ($items as $item) {
            $groupKey = $item[$key] ?? null;
            if ($groupKey) {
                $result[$groupKey][] = $item;
            }
        }

        return $result;
    }
}//end class
