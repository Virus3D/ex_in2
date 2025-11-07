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
use App\Entity\Transfer;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transfer>
 */
final class TransferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transfer::class);
    }//end __construct()

    /**
     * @return Transfer[]
     */
    public function list(DateTime $startDate, DateTime $endDate, ?Card $cardOut, ?Card $cardIn): array
    {
        $queryBuilder = $this->createQueryBuilder('t');

        $query = $queryBuilder
            ->andWhere('t.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('t.date', 'DESC');
        if ($cardOut) {
            $query->andWhere('t.cardOut = :cardOut')
                ->setParameter('cardOut', $cardOut);
        }
        if ($cardIn) {
            $query->andWhere('t.cardIn = :cardIn')
                ->setParameter('cardIn', $cardIn);
        }

        return $query->getQuery()
            ->getResult();
    }//end list()
}//end class
