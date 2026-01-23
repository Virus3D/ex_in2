<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Place;
use App\Entity\ServiceMeterReading;
use Doctrine\ORM\EntityManagerInterface;

final class ServiceMeterReadingService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }//end __construct()

    /**
     * Получить показания счетчиков по году и месту.
     *
     * @return array<int, array<int, int>> Массив [service_id][month] => reading
     */
    public function getReadings(Place $place, int $year): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $data         = [];
        $result       = $queryBuilder
            ->select('IDENTITY(smr.service) as service_id, smr.month, smr.reading')
            ->from(ServiceMeterReading::class, 'smr')
            ->andWhere('smr.place = :place')
            ->andWhere('smr.year = :year')
            ->setParameter('place', $place)
            ->setParameter('year', $year)
            ->orderBy('smr.month', 'ASC')
            ->getQuery()
            ->getResult();
        foreach ($result as $row) {
            $data[(int) $row['service_id']][(int) $row['month']] = (int) $row['reading'];
        }

        return $data;
    }//end getReadings()

    /**
     * Рассчитать расход за месяц на основе показаний счетчиков.
     *
     * @param array<int, array<int, int>> $readings Показания счетчиков [service_id][month] => reading
     * @param Place                       $place     Место
     * @param int                         $year      Год
     *
     * @return array<int, array<int, int|null>> Массив [service_id][month] => consumption (null если нет данных)
     */
    public function calculateConsumption(array $readings, Place $place, int $year): array
    {
        $consumption = [];

        foreach ($readings as $serviceId => $months) {
            $consumption[$serviceId] = [];

            foreach ($months as $month => $reading) {
                $previousMonth = $month === 1 ? 12 : ($month - 1);
                $previousYear  = $month === 1 ? ($year - 1) : $year;

                // Ищем предыдущее показание.
                $previousReading = null;
                if (isset($readings[$serviceId][$previousMonth]) && $previousYear === $year) {
                    // В пределах одного года.
                    $previousReading = $readings[$serviceId][$previousMonth];
                } else if ($previousYear < $year) {
                    // Для января нужно получить показание за декабрь предыдущего года.
                    $previousYearReadings = $this->getReadings($place, $previousYear);
                    if (isset($previousYearReadings[$serviceId][12])) {
                        $previousReading = $previousYearReadings[$serviceId][12];
                    }
                }//end if

                // Если есть предыдущее показание, рассчитываем расход.
                if ($previousReading !== null) {
                    $consumption[$serviceId][$month] = $reading - $previousReading;
                } else {
                    $consumption[$serviceId][$month] = null;
                }//end if
            }//end foreach
        }

        return $consumption;
    }//end calculateConsumption()

    /**
     * Сохранить показание счетчика.
     */
    public function saveReading(
        Place $place,
        ServiceMeterReading $meterReading,
        int $year,
    ): void {
        // Проверяем, существует ли уже показание для этого периода.
        $existing = $this->entityManager->getRepository(ServiceMeterReading::class)->findOneBy(
            [
                'service' => $meterReading->getService(),
                'place'   => $place,
                'year'    => $year,
                'month'   => $meterReading->getMonth(),
            ]
        );

        if ($existing !== null) {
            $existing->setReading($meterReading->getReading());
        } else {
            $meterReading
                ->setPlace($place)
                ->setYear($year);
            $this->entityManager->persist($meterReading);
        }//end if

        $this->entityManager->flush();
    }//end saveReading()
}//end class
