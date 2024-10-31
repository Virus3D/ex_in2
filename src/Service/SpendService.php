<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Card;
use App\Entity\Spend;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

use function count;

final class SpendService
{
    public function __construct(private EntityManagerInterface $entityManager) {}//end __construct()

    /**
     * @param Card[] $cards
     *
     * @return array<int, int>
     */
    public function getCardsSummary(iterable $cards, DateTime $startDate, DateTime $endDate): array
    {
        $cardIds = [];

        // Преобразуем в массив ID для карт
        foreach ($cards as $card)
        {
            $cardIds[] = $card->getId();
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('IDENTITY(s.card) AS cardId, SUM(s.balance) AS totalBalance')
            ->from(Spend::class, 's')
            ->where('s.card IN (:cardIds)')
            ->andWhere('s.date BETWEEN :startDate AND :endDate')
            ->groupBy('s.card')
            ->setParameter('cardIds', $cardIds)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
        ;

        $results = $queryBuilder->getQuery()->getResult();

        $summary = array_combine($cardIds, array_fill(0, count($cardIds), 0));
        foreach ($results as $result)
        {
            $summary[(int) $result['cardId']] = (int) $result['totalBalance'];
        }

        return $summary;
    }//end getCardsSummary()
}//end class
