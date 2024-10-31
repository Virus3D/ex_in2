<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Helper;

use DateTime;
use Symfony\Component\HttpFoundation\Request;

final class FilterDataHelper
{
    /** @phpstan-ignore shipmonk.publicPropertyNotReadonly */
    public static DateTime $startDate;

    /** @phpstan-ignore shipmonk.publicPropertyNotReadonly */
    public static DateTime $endDate;

    /**
     * @return array<string, mixed>
     */
    public static function getFilterData(Request $request): array
    {
        // Получение данных и фильтрация из сессии
        $session    = $request->getSession();
        $filterData = $session->get('filter_data', []);

        $filterData['year']  ??= (int) date('Y');
        $filterData['month'] ??= (int) date('m');

        self::$startDate = new DateTime("{$filterData['year']}-{$filterData['month']}-01");
        self::$endDate   = (clone self::$startDate)->modify('last day of this month');

        return $filterData;
    }//end getFilterData()
}//end class
