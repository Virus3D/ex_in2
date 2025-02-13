<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Receipt;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Receipt>
 */
final class ReceiptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Receipt::class);
    }//end __construct()

    /**
     * @return Receipt[]
     */
    public function list(DateTime $startDate, DateTime $endDate, ?Card $card): array
    {
        $queryBuilder = $this->createQueryBuilder('r');

        $query = $queryBuilder
            ->andWhere('r.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('r.date', 'DESC');
        if ($card) {
            $query->andWhere('r.card = :card')
                ->setParameter('card', $card);
        }

        return $query->getQuery()
            ->getResult();
    }//end list()
}//end class
