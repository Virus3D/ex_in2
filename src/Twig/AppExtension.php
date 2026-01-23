<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Twig;

use DateTimeInterface;
use DateTimeZone;
use Money\Currency;
use Money\Money;
use NumberFormatter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class AppExtension extends AbstractExtension
{
    public function __construct(
        private readonly ?ParameterBagInterface $parameterBag = null
    ) {
    }//end __construct()

    /**
     * @return array<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('formatCurrency', [$this, 'formatCurrency']),
            new TwigFilter('group_by', [$this, 'groupBy']),
            new TwigFilter('date_timezone', [$this, 'dateTimezone']),
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
    }//end groupBy()

    /**
     * Форматировать дату с учетом часового пояса пользователя.
     *
     * @param DateTimeInterface|string|null $date   Дата для форматирования
     * @param string                         $format Формат даты (по умолчанию 'Y-m-d H:i')
     * @param string|null                    $timezone Часовой пояс (по умолчанию из конфигурации или 'UTC')
     *
     * @return string Отформатированная дата
     */
    public function dateTimezone(
        DateTimeInterface|string|null $date,
        string $format = 'Y-m-d H:i',
        ?string $timezone = null
    ): string {
        if ($date === null) {
            return '';
        }

        // Получаем часовой пояс из параметра или используем переданный
        $userTimezone = $timezone ?? $this->parameterBag?->get('app.user_timezone') ?? 'UTC';

        try {
            $timezoneObj = new DateTimeZone($userTimezone);
        } catch (\Exception $e) {
            // Если часовой пояс невалидный, используем UTC
            $timezoneObj = new DateTimeZone('UTC');
        }

        // Преобразуем дату в объект DateTime, если это строка
        if (is_string($date)) {
            $dateObj = new \DateTime($date);
        } else {
            // Создаем новый объект DateTime из DateTimeInterface
            $dateObj = \DateTime::createFromInterface($date);
        }

        // Устанавливаем часовой пояс
        $dateObj->setTimezone($timezoneObj);

        return $dateObj->format($format);
    }//end dateTimezone()
}//end class
