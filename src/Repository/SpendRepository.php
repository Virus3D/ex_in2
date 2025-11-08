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

    /**
     * Получить уникальные комментарии из базы данных.
     *
     * @return string[]
     */
    public function getUniqueComments(): array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        $result = $queryBuilder
            ->select('DISTINCT s.comment')
            ->where('s.comment IS NOT NULL')
            ->andWhere('s.comment != :empty')
            ->setParameter('empty', '')
            ->orderBy('s.comment', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($result, 'comment');
    }//end getUniqueComments()

    /**
     * Получить данные для диаграммы расходов по комментариям.
     *
     * @return array<string, int>
     */
    public function getExpensesByComment(DateTime $startDate, DateTime $endDate, ?Card $card): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('s.comment as comment_name, SUM(s.balance) as total_amount')
            ->andWhere('s.date BETWEEN :startDate AND :endDate')
            ->andWhere('s.comment IS NOT NULL')
            ->andWhere('s.comment != :empty')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('empty', '')
            ->groupBy('s.comment')
            ->orderBy('total_amount', 'DESC');

        if ($card) {
            $queryBuilder->andWhere('s.card = :card')
                ->setParameter('card', $card);
        }

        $result = $queryBuilder->getQuery()->getScalarResult();

        $expenses = [];
        foreach ($result as $row) {
            $comment = $row['comment_name'] ?: 'Без комментария';

            // Объединяем комментарии, начинающиеся с "Service".
            if (0 === mb_stripos($comment, 'Service')) {
                $comment = 'Service';
            }

            if (isset($expenses[$comment])) {
                $expenses[$comment] += (int) $row['total_amount'];
            } else {
                $expenses[$comment] = (int) $row['total_amount'];
            }
        }

        // Сортируем по убыванию суммы.
        arsort($expenses);

        return $expenses;
    }//end getExpensesByComment()

    /**
     * Получить данные для диаграммы расходов по дням.
     *
     * @return array<string, int>
     */
    public function getExpensesByDay(DateTime $startDate, DateTime $endDate, ?Card $card): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select("s.date, SUM(s.balance) as total_amount")
            ->andWhere('s.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('s.date')
            ->orderBy('s.date', 'ASC');

        if ($card) {
            $queryBuilder->andWhere('s.card = :card')
                ->setParameter('card', $card);
        }

        $result = $queryBuilder->getQuery()->getScalarResult();

        $expenses = [];
        foreach ($result as $row) {
            $date   = new DateTime($row['date']);
            $dayKey = $date->format('d.m');

            if (!isset($expenses[$dayKey])) {
                $expenses[$dayKey] = 0;
            }
            $expenses[$dayKey] += (int) $row['total_amount'];
        }

        return $expenses;
    }//end getExpensesByDay()
}//end class
