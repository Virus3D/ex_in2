<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Helper;

use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class FilterDataHelper
{
    /**
     * @phpstan-ignore shipmonk.publicPropertyNotReadonly
     */
    public static DateTime $startDate;

    /**
     * @phpstan-ignore shipmonk.publicPropertyNotReadonly
     */
    public static DateTime $endDate;

    /**
     * @phpstan-ignore shipmonk.publicPropertyNotReadonly
     */
    public static int $year;

    /**
     * @phpstan-ignore shipmonk.publicPropertyNotReadonly
     */
    public static int $month;

    /**
     * @return array<string, int|string>
     */
    public static function getFilterData(Request $request): array
    {
        // Получение данных и фильтрация из сессии
        $session = $request->getSession();
        $filterData = $session->get('filter_data', []);

        $filterData['year']  ??= (int) date('Y');
        $filterData['month'] ??= (int) date('m');

        self::$startDate = new DateTime("{$filterData['year']}-{$filterData['month']}-01 00:00:00");
        self::$endDate   = (clone self::$startDate)->modify('last day of this month')->setTime(23, 59, 59);

        self::$year  = (int) $filterData['year'];
        self::$month = (int) $filterData['month'] - 1;
        if (0 == self::$month) {
            --self::$year;
            self::$month = 12;
        }

        return $filterData;
    }//end getFilterDataSession()
}//end class
