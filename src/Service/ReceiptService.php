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

final class ReceiptService
{
    public function __construct(private EntityManagerInterface $entityManager) {}//end __construct()

    /**
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
            ->setParameter('endDate', $endDate)
        ;

        $results = $queryBuilder->getQuery()->getResult();

        foreach ($results as $result)
        {
            /**
             * @var Card $card
             */
            $card = $result['card'];

            $totalBalance = (int) $result['totalBalance'];
            $card->setTotalReceipt($card->getTotalReceipt() + $totalBalance);
            if (CardService::DEBIT_CARD === $card->getType())
            {
                $category = $card->getCategory();
                $category->setTotalReceipt($category->getTotalReceipt() + $totalBalance);
            }
        }
    }//end getCardsSummary()
}//end class
