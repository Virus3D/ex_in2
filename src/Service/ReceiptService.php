<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Card;
use App\Entity\Receipt;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

use function count;

final class ReceiptService
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
        $queryBuilder->select('IDENTITY(r.card) AS cardId, SUM(r.balance) AS totalBalance')
            ->from(Receipt::class, 'r')
            ->where('r.card IN (:cardIds)')
            ->andWhere('r.date BETWEEN :startDate AND :endDate')
            ->groupBy('r.card')
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
