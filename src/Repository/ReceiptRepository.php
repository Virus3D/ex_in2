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
    }// end __construct()

    /**
     * Список.
     *
     * @return Receipt[]
     */
    public function list(DateTime $startDate, DateTime $endDate, ?Card $card): array
    {
        $queryBuilder = $this->createQueryBuilder('r');

        $query = $queryBuilder
            ->andWhere('r.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('r.date', 'DESC')
            ->addOrderBy('r.id', 'DESC');
        if ($card) {
            $query->andWhere('r.card = :card')
                ->setParameter('card', $card);
        }

        return $query->getQuery()
            ->getResult();
    }// end list()

    /**
     * Получить уникальные комментарии из базы данных.
     *
     * @return string[]
     */
    public function getUniqueComments(): array
    {
        $queryBuilder = $this->createQueryBuilder('r');

        $result = $queryBuilder
            ->select('DISTINCT r.comment')
            ->where('r.comment IS NOT NULL')
            ->andWhere('r.comment != :empty')
            ->setParameter('empty', '')
            ->orderBy('r.comment', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($result, 'comment');
    }// end getUniqueComments()

    /**
     * Получить данные для диаграммы поступлений по комментариям.
     *
     * @return array<string, int>
     */
    public function getReceiptsByComment(DateTime $startDate, DateTime $endDate, ?Card $card): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->select('r.comment as comment_name, SUM(r.balance) as total_amount')
            ->andWhere('r.date BETWEEN :startDate AND :endDate')
            ->andWhere('r.comment IS NOT NULL')
            ->andWhere('r.comment != :empty')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('empty', '')
            ->groupBy('r.comment')
            ->orderBy('total_amount', 'DESC');

        if ($card) {
            $queryBuilder->andWhere('r.card = :card')
                ->setParameter('card', $card);
        }

        $result = $queryBuilder->getQuery()->getScalarResult();

        $receipts = [];
        foreach ($result as $row) {
            $comment = $row['comment_name'] ?: 'Без комментария';

            if (isset($receipts[$comment])) {
                $receipts[$comment] += (int) $row['total_amount'];
            } else {
                $receipts[$comment] = (int) $row['total_amount'];
            }
        }

        // Сортируем по убыванию суммы.
        arsort($receipts);

        return $receipts;
    }// end getReceiptsByComment()

    /**
     * Получить данные для диаграммы поступлений по дням.
     *
     * @return array<string, int>
     */
    public function getReceiptsByDay(DateTime $startDate, DateTime $endDate, ?Card $card): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->select("r.date, SUM(r.balance) as total_amount")
            ->andWhere('r.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('r.date')
            ->orderBy('r.date', 'ASC');

        if ($card) {
            $queryBuilder->andWhere('r.card = :card')
                ->setParameter('card', $card);
        }

        $result = $queryBuilder->getQuery()->getScalarResult();

        $receipts = [];
        foreach ($result as $row) {
            $date   = new DateTime($row['date']);
            $dayKey = $date->format('d.m');

            if (!isset($receipts[$dayKey])) {
                $receipts[$dayKey] = 0;
            }
            $receipts[$dayKey] += (int) $row['total_amount'];
        }

        return $receipts;
    }// end getReceiptsByDay()
}// end class
