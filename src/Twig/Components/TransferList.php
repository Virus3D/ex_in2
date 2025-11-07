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
use App\Entity\Transfer;
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
final class TransferList
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?Card $selectedCardIn = null;

    #[LiveProp(writable: true)]
    public ?Card $selectedCardOut = null;

    public string $fieldIn = 'transferCardIn';

    public string $fieldOut = 'transferCardOut';

    public int $total = 0;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private CardService $cardService,
        private FilterDataHelper $filterDataHelper
    ) {
        $this->setSelectedCard();
    }//end __construct()

    /**
     * Задать карты для фильтра.
     */
    private function setSelectedCard(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $cardRepository = $this->entityManager->getRepository(Card::class);

        $cardIdIn = $request->getSession()->get($this->fieldIn);

        $this->selectedCardIn = $cardIdIn ? $cardRepository->find($cardIdIn) : null;

        $cardIdOut = $request->getSession()->get($this->fieldOut);

        $this->selectedCardOut = $cardIdOut ? $cardRepository->find($cardIdOut) : null;
    }//end setSelectedCard()

    /**
     * Получить список переводов.
     *
     * @return Transfer[]
     */
    public function getTransferList(): array
    {
        $this->setSelectedCard();

        return $this->entityManager->getRepository(Transfer::class)->list(
            $this->filterDataHelper->startDate,
            $this->filterDataHelper->endDate,
            $this->selectedCardOut,
            $this->selectedCardIn
        );
    }//end getTransferList()

    /**
     * Удаление записи перевода.
     */
    #[LiveAction]
    public function remove(#[LiveArg] int $id): void
    {
        $receipt = $this->entityManager->getRepository(Transfer::class)->find($id);
        $this->cardService->changeBalance($receipt->getCardOut(), $receipt->getBalance());
        $this->cardService->changeBalance($receipt->getCardIn(), -$receipt->getBalance());
        $this->entityManager->remove($receipt);
        $this->entityManager->flush();

        $this->emit('transferDeleted');
    }//end remove()

    /**
     * Добавить перевод.
     */
    #[LiveListener('transferAdded')]
    #[LiveListener('updateCard')]
    public function onTransferAdded(): void
    {
        $this->setSelectedCard();
    }//end onTransferAdded()
}//end class
