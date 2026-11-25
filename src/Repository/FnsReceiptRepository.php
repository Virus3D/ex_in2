<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FnsReceipt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FnsReceipt>
 */
final class FnsReceiptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FnsReceipt::class);
    }// end __construct()

    /**
     * Найти чек по ключу ФНС.
     */
    public function findByKey(string $receiptKey): ?FnsReceipt
    {
        return $this->findOneBy(['receiptKey' => $receiptKey]);
    }// end findByKey()

    /**
     * Проверить, существует ли чек с данным ключом.
     */
    public function existsByKey(string $receiptKey): bool
    {
        return $this->findOneBy(['receiptKey' => $receiptKey]) !== null;
    }// end existsByKey()

    /**
     * Найти не связанные чеки по дате и сумме (±1 день, ±1 рубль).
     *
     * @return FnsReceipt[]
     */
    public function findUnlinkedByCriteria(DateTime $date, int $amount): array
    {
        $start = clone $date;
        $start->modify('-1 day');
        $end = clone $date;
        $end->modify('+1 day');

        // ±1 рубль = ±100 копеек.
        $tolerance = 100;

        return $this->createQueryBuilder('r')
            ->where('r.fiscalDate BETWEEN :start AND :end')
            ->andWhere('r.totalSum BETWEEN :minSum AND :maxSum')
            ->andWhere('r.id NOT IN (SELECT s.fnsReceipt FROM App\Entity\Spend s WHERE s.fnsReceipt IS NOT NULL)')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('minSum', $amount - $tolerance)
            ->setParameter('maxSum', $amount + $tolerance)
            ->orderBy('r.fiscalDate', 'DESC')
            ->getQuery()
            ->getResult();
    }// end findUnlinkedByCriteria()
}// end class
