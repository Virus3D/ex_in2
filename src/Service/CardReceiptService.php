<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Card;
use App\Entity\Receipt;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

final class CardReceiptService
{
    public function __construct(private EntityManagerInterface $entityManager) {}//end __construct()

    /**
     * Подсчет поступлений на карты.
     *
     * @param Card[] $cards
     */
    public function getCardsSummary(iterable $cards, DateTime $startDate, DateTime $endDate): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('SUM(r.balance) AS totalBalance')
            ->from(Receipt::class, 'r')
            ->join(Card::class, 'c', 'WITH', 'r.card = c.id')
            ->addSelect('c as card')
            ->where('r.card IN (:cardIds)')
            ->andWhere('r.date BETWEEN :startDate AND :endDate')
            ->groupBy('r.card')
            ->setParameter('cardIds', $cards)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        $results = $queryBuilder->getQuery()->getResult();

        foreach ($results as $result) {
            /**
             * @var Card $card
             */
            $card = $result['card'];

            $totalBalance = (int) $result['totalBalance'];
            $card->addTotalReceipt($totalBalance);
            if (CardService::DEBIT_CARD === $card->getType()) {
                $card->getCategory()?->addTotalReceipt($totalBalance);
            }
        }
    }//end getCardsSummary()
}//end class
