<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Subscription;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 */
final class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }//end __construct()

    /**
     * Найти все подписки, у которых дата следующего платежа наступила или прошла.
     *
     * @param DateTime $date Текущая дата
     *
     * @return Subscription[]
     */
    public function findAllDueSubscriptions(DateTime $date): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.nextPaymentDate <= :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
        ;
    }//end findAllDueSubscriptions()
}//end class
