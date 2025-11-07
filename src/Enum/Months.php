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
 * Months enumeration for form choices.
 */
final class Months
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
     * Get months as choices array for forms.
     *
     * @return array<string, int>
     */
    public static function getChoices(): array
    {
        return [
            'January'   => self::JANUARY,
            'February'  => self::FEBRUARY,
            'March'     => self::MARCH,
            'April'     => self::APRIL,
            'May'       => self::MAY,
            'June'      => self::JUNE,
            'July'      => self::JULY,
            'August'    => self::AUGUST,
            'September' => self::SEPTEMBER,
            'October'   => self::OCTOBER,
            'November'  => self::NOVEMBER,
            'December'  => self::DECEMBER,
        ];
    }//end getChoices()
}//end class
