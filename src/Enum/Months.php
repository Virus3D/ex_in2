<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Enum;

/**
 * Перечисление месяцев года.
 *
 * Используется для выбора месяца в формах и фильтрации данных.
 * Обеспечивает единый источник истины для всех месяцев.
 *
 * @package App\Enum
 */
enum Months: int
{
    public const JANUARY = 1;

    public const FEBRUARY = 2;

    public const MARCH = 3;

    public const APRIL = 4;

    public const MAY = 5;

    public const JUNE = 6;

    public const JULY = 7;

    public const AUGUST = 8;

    public const SEPTEMBER = 9;

    public const OCTOBER = 10;

    public const NOVEMBER = 11;

    public const DECEMBER = 12;

    /**
     * Получение массива для выпадающего списка.
     *
     * @return array<string, int> Ассоциативный массив [название => значение]
     */
    public static function getChoices(): array
    {
        return [
            'month.January'   => self::JANUARY,
            'month.February'  => self::FEBRUARY,
            'month.March'     => self::MARCH,
            'month.April'     => self::APRIL,
            'month.May'       => self::MAY,
            'month.June'      => self::JUNE,
            'month.July'      => self::JULY,
            'month.August'    => self::AUGUST,
            'month.September' => self::SEPTEMBER,
            'month.October'   => self::OCTOBER,
            'month.November'  => self::NOVEMBER,
            'month.December'  => self::DECEMBER,
        ];
    }// end getChoices()

    /**
     * Получение предыдущего месяца.
     *
     * @return self Предыдущий месяц
     */
    public function getPrevious(): self
    {
        $value = $this->value === 1 ? 12 : $this->value - 1;
        return self::from($value);
    }// end getPrevious()

    /**
     * Получение текущего месяца.
     *
     * @return self Текущий месяц
     */
    public static function getCurrent(): self
    {
        return self::from((int) (new \DateTime())->format('n'));
    }// end getCurrent()

    /**
     * Получение предыдущего месяца относительно текущего.
     *
     * @return self Предыдущий месяц
     */
    public static function getPreviousMonth(): self
    {
        return self::getCurrent()->getPrevious();
    }// end getPreviousMonth()
}// end enum
