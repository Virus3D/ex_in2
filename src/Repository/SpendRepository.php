<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Spend;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Spend>
 */
final class SpendRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Spend::class);
    }//end __construct()

    /**
     * @return Spend[]
     */
    public function list(DateTime $startDate, DateTime $endDate, ?Card $card): array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        $query = $queryBuilder
            ->andWhere('s.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('s.date', 'DESC');
        if ($card) {
            $query->andWhere('s.card = :card')
                ->setParameter('card', $card);
        }

        return $query->getQuery()
            ->getResult();
    }//end list()
}//end class
