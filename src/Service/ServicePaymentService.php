<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Place;
use App\Entity\ServicePayment;
use Doctrine\ORM\EntityManagerInterface;

final class ServicePaymentService
{
    public function __construct(private EntityManagerInterface $entityManager) {}//end __construct()

    /**
     * @return array<int, array<int, array<array<string, mixed>>>>
     */
    public function handle(Place $place, int $year): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $data         = [];
        $result       = $queryBuilder
            ->select('IDENTITY(sp.service) as service_id, sp.month, sp.amount, sp.date')
            ->from(ServicePayment::class, 'sp')
            ->andWhere('sp.place = :place')
            ->andWhere('sp.year = :year')
            ->setParameter('place', $place)
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult()
        ;
        foreach ($result as $row)
        {
            $data[(int) $row['service_id']][(int) $row['month']][] = ['amount' => $row['amount'], 'date' => $row['date']];
        }

        return $data;
    }//end handle()
}//end class
