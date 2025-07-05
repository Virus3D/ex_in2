<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Helper;

use DateTime;
use Symfony\Component\HttpFoundation\RequestStack;

final class FilterDataHelper
{
    public readonly DateTime $startDate;

    public readonly DateTime $endDate;

    public readonly int $year;

    public readonly int $month;

    public readonly int $yearPrev;

    public readonly int $monthPrev;

    public function __construct(RequestStack $requestStack)
    {
        $session = $requestStack->getSession();
        $filterData = $session->get('filter_data', []);

        $this->year  = (int) ($filterData['year'] ?? date('Y'));
        $this->month = (int) ($filterData['month'] ?? date('m'));

        $this->startDate = new DateTime("{$this->year}-{$this->month}-01 00:00:00");
        $this->endDate   = (clone $this->startDate)->modify('last day of this month')->setTime(23, 59, 59);

        $yearPrev  = $this->year;
        $monthPrev = $this->month - 1;

        if (0 == $monthPrev) {
            --$yearPrev;
            $monthPrev = 12;
        }

        $this->yearPrev  = $yearPrev;
        $this->monthPrev = $monthPrev;
    }//end __construct()

    public function toArray(): array
    {
        return [
            'year'      => $this->year,
            'month'     => $this->month,
        ];
    }
}//end class
