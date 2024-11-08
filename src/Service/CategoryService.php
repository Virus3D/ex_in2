<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\CardCategory;
use App\Helper\FilterDataHelper;

final class CategoryService
{
    public function __construct(private ReceiptService $receiptService, private SpendService $spendService, private TransferService $transferService) {}//end __construct()

    public function handle(CardCategory $category): void
    {
        $cards = $category->getCards();

        $this->receiptService->getCardsSummary($cards, FilterDataHelper::$startDate, FilterDataHelper::$endDate);
        $this->spendService->getCardsSummary($cards, FilterDataHelper::$startDate, FilterDataHelper::$endDate);
        $this->transferService->getCardsSummary($cards, FilterDataHelper::$startDate, FilterDataHelper::$endDate);

        foreach ($cards as $card)
        {
            if (CardService::DEBIT_CARD === $card->getType())
            {
                $category->setBalance($category->getBalance() + $card->getBalance());
            }
        }
    }//end handle()
}//end class
