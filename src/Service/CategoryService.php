<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\CardCategory;
use App\Helper\FilterDataHelper;

final class CategoryService
{
    public function __construct(
        private CardReceiptService $receiptService,
        private CardSpendService $spendService,
        private CardTransferService $transferService,
    ) {}//end __construct()

    /**
     * Получает информацию по картам категории.
     */
    public function handle(CardCategory $category): void
    {
        $cards = $category->getCards();

        $this->clear($cards);

        $this->receiptService->getCardsSummary($cards, FilterDataHelper::$startDate, FilterDataHelper::$endDate);
        $this->spendService->getCardsSummary($cards, FilterDataHelper::$startDate, FilterDataHelper::$endDate);
        $this->transferService->getCardsSummary($cards, FilterDataHelper::$startDate, FilterDataHelper::$endDate);

        $this->calcTotalBalance($category, $cards);
    }//end handle()

    private function clear(iterable $cards): void
    {
        foreach ($cards as $card) {
            $card->setTotalSpend(0);
            $card->setTotalReceipt(0);
        }
    }//end clear()

    private function calcTotalBalance(CardCategory $category, iterable $cards): void
    {
        foreach ($cards as $card) {
            if (CardService::DEBIT_CARD === $card->getType()) {
                $category->setBalance($category->getBalance() + $card->getBalance());
            }
        }
    }//end clear()
}//end class
