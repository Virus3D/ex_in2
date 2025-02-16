<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Card;
use App\Entity\Receipt;
use App\Helper\FilterDataHelper;
use App\Service\CardService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ReceiptList
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?Card $selectedCard = null;

    public string $field = 'receiptCard';

    public int $total = 0;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private CardService $cardService,
    ) {
        $this->setSelectedCard();
    }//end __construct()

    /**
     * Задать карты для фильтра.
     */
    private function setSelectedCard(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        FilterDataHelper::getFilterData($request);
        $cardRepository = $this->entityManager->getRepository(Card::class);

        $cardId = $request->getSession()->get($this->field);

        $this->selectedCard = $cardId ? $cardRepository->find($cardId) : null;
    }//end setSelectedCard()

    /**
     * Получить список поступлений.
     *
     * @return Receipt[]
     */
    public function getReceiptList(): array
    {
        $this->setSelectedCard();

        $receiptList = $this->entityManager->getRepository(Receipt::class)->list(
            FilterDataHelper::$startDate,
            FilterDataHelper::$endDate,
            $this->selectedCard
        );

        $this->total = array_reduce($receiptList, static fn ($carry, $receipt) => $carry + $receipt->getBalance(), 0);

        return $receiptList;
    }//end getReceiptList()

    /**
     * Удаление записи поступления.
     */
    #[LiveAction]
    public function remove(#[LiveArg] int $id): void
    {
        $receipt = $this->entityManager->getRepository(Receipt::class)->find($id);
        $this->cardService->changeBalance($receipt->getCard(), -$receipt->getBalance());
        $this->entityManager->remove($receipt);
        $this->entityManager->flush();

        $this->emit('receiptDeleted');
    }//end remove()

    /**
     * Добавить поступление.
     */
    #[LiveListener('receiptAdded')]
    #[LiveListener('updateCard')]
    public function onReceiptAdded(): void
    {
        $this->setSelectedCard();
    }//end onReceiptAdded()
}//end class
