<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SubscriptionAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubscriptionAccount>
 */
class SubscriptionAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriptionAccount::class);
    }// end __construct()
}// end class
