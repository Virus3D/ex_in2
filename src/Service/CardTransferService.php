<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Transfer;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

use function in_array;

final class CardTransferService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }// end __construct()

    /**
     * Суммы транзакций по картам.
     */
    public function getCardsSummary(Collection $cards, DateTime $startDate, DateTime $endDate): void
    {
        $query = $this->entityManager
            ->createQuery(
                'SELECT t, cIn, cOut
                    FROM ' . Transfer::class . ' t
                    JOIN t.cardIn cIn
                    JOIN t.cardOut cOut
                    WHERE (t.cardIn IN (:cardIds) or t.cardOut IN (:cardIds)) AND t.date BETWEEN :startDate AND :endDate'
            )
            ->setParameter('cardIds', $cards)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        $results = $query->getResult();

        foreach ($results as $result) {
            $cardIn  = $result->getCardIn();
            $cardOut = $result->getCardOut();

            $totalBalance = $result->getBalance();
            $cardIn->addTotalTransferAdd($totalBalance);
            $cardOut->addTotalTransferSub($totalBalance);
            if (in_array($cardIn->getType(), [CardService::CREDIT, CardService::CREDIT_CARD], true)) {
                $cardOut->getCategory()?->addTotalSpend($totalBalance);
            }
        }
    }// end getCardsSummary()
}// end class
