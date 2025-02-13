<?php

/**
 * Expenses/Income
 *
 * @licens
 * e Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Card;
use App\Entity\Receipt;
use App\Helper\FilterDataHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ReceiptListComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?Card $selectedCard = null;

    public string $field = 'receiptCard';

    public int $total = 0;

    public function __construct(private EntityManagerInterface $entityManager, private RequestStack $requestStack) {}

    public function getReceiptList(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        FilterDataHelper::getFilterData($request);

        $receiptCardId = $request->getSession()->get($this->field);
        $cardRepository = $this->entityManager->getRepository(Card::class);
        $this->selectedCard = ($receiptCardId) ? $cardRepository->find($receiptCardId) : null;


        $receiptList = $this->entityManager->getRepository(Receipt::class)->list(
            FilterDataHelper::$startDate,
            FilterDataHelper::$endDate,
            $this->selectedCard
        );

        $this->total = array_reduce($receiptList, function ($carry, $receipt) {
            return $carry + $receipt->getBalance();
        }, 0);

        return $receiptList;
    }

    public function updateSelectedValue(string $value): void {
        $this->selectedValue = $value;
    }
}
