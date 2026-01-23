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
use App\Entity\CardCategory;
use App\Helper\FilterDataHelper;

final class CategoryService
{
    public function __construct(
        private CardReceiptService $receiptService,
        private CardSpendService $spendService,
        private CardTransferService $transferService,
        private FilterDataHelper $filterDataHelper
    ) {
    }// end __construct()

    /**
     * Получает информацию по картам категории.
     */
    public function handle(CardCategory $category): void
    {
        $cards = $category->getCards();

        $this->clear($cards);

        $this->receiptService->getCardsSummary(
            $cards,
            $this->filterDataHelper->startDate,
            $this->filterDataHelper->endDate
        );
        $this->spendService->getCardsSummary(
            $cards,
            $this->filterDataHelper->startDate,
            $this->filterDataHelper->endDate
        );
        $this->transferService->getCardsSummary(
            $cards,
            $this->filterDataHelper->startDate,
            $this->filterDataHelper->endDate
        );

        $this->calcTotalBalance($category, $cards);
    }// end handle()

    /**
     * Resets total spend and total receipt of all cards.
     */
    private function clear(iterable $cards): void
    {
        foreach ($cards as $card) {
            $card->setTotalSpend(0);
            $card->setTotalReceipt(0);
        }
    }// end clear()

    /**
     * Calculates and sets the total balance for the given category from its debit cards.
     */
    private function calcTotalBalance(CardCategory $category, iterable $cards): void
    {
        $totalBalance = 0;
        foreach ($cards as $card) {
            if (CardService::DEBIT_CARD === $card->getType()) {
                $totalBalance += $card->getBalance();
            }
        }

        $category->setBalance($totalBalance);
    }// end calcTotalBalance()
}// end class
