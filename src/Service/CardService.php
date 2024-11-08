<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;

final class CardService
{
    public const DEBIT_CARD = 0;

    public const CREDIT_CARD = 1;

    public const SAVINGS_ACCOUNT = 2;

    public const CREDIT = 3;

    public function __construct(private EntityManagerInterface $entityManager) {}//end __construct()

    public function changeBalance(Card $card, int $balance): void
    {
        $card->setBalance($card->getBalance() + $balance);
        $this->entityManager->flush();
    }//end changeBalance()
}//end class
