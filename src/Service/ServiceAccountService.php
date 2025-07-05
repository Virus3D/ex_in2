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
use App\Entity\ServiceAccount;
use Doctrine\ORM\EntityManagerInterface;

final class ServiceAccountService
{
    public function __construct(private EntityManagerInterface $entityManager) {}//end __construct()

    /**
     * @return array<int, array<int, array<string, mixed>>>
     */
    public function handle(Place $place, int $year): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $data         = [];
        $result       = $queryBuilder
            ->select('IDENTITY(sa.service) as service_id, sa.month, sa.amount')
            ->from(ServiceAccount::class, 'sa')
            ->andWhere('sa.place = :place')
            ->andWhere('sa.year = :year')
            ->setParameter('place', $place)
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult();
        foreach ($result as $row) {
            $data[(int) $row['service_id']][(int) $row['month']] = $row['amount'];
        }

        return $data;
    }//end handle()

    public function createAccount(
        Place $place,
        ServiceAccount $serviceAccount,
        int $year,
    ): void {
        $serviceAccount
            ->setPlace($place)
            ->setYear($year);
        $this->entityManager->persist($serviceAccount);
        $this->entityManager->flush();
    }//end createAccount()
}//end class
