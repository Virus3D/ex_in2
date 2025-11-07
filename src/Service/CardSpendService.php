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
use App\Entity\Spend;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

use function in_array;

final class CardSpendService
{
    public function __construct(private EntityManagerInterface $entityManager) {}//end __construct()

    /**
     * @param Card[] $cards
     */
    public function getCardsSummary(iterable $cards, DateTime $startDate, DateTime $endDate): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('SUM(s.balance) AS totalBalance')
            ->from(Spend::class, 's')
            ->join(Card::class, 'c', 'WITH', 's.card = c.id')
            ->addSelect('c as card')
            ->where('s.card IN (:cardIds)')
            ->andWhere('s.date BETWEEN :startDate AND :endDate')
            ->groupBy('s.card')
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
            $card->addTotalSpend($totalBalance);
            if (in_array($card->getType(), [CardService::DEBIT_CARD, CardService::CREDIT_CARD], true)) {
                $card->getCategory()?->addTotalSpend($totalBalance);
            }
        }
    }//end getCardsSummary()
}//end class
