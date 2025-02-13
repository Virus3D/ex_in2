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
use App\Entity\Spend;
use App\Entity\Transfer;
use App\Helper\FilterDataHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class TransferListComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?Card $selectedCardIn = null;

    #[LiveProp(writable: true)]
    public ?Card $selectedCardOut = null;

    public string $fieldIn = 'transferCardIn';

    public string $fieldOut = 'transferCardOut';

    public int $total = 0;

    public function __construct(private EntityManagerInterface $entityManager, private RequestStack $requestStack)
    {
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

        $cardIdIn = $request->getSession()->get($this->fieldIn);

        $this->selectedCardIn = $cardIdIn ? $cardRepository->find($cardIdIn) : null;

        $cardIdOut = $request->getSession()->get($this->fieldOut);

        $this->selectedCardOut = $cardIdOut ? $cardRepository->find($cardIdOut) : null;
    }//end setSelectedCard()

    /**
     * Получить список переводов.
     *
     * @return Spend[]
     */
    public function getTransferList(): array
    {
        $this->setSelectedCard();

        return $this->entityManager->getRepository(Transfer::class)->list(
            FilterDataHelper::$startDate,
            FilterDataHelper::$endDate,
            $this->selectedCardOut,
            $this->selectedCardIn
        );
    }//end getTransferList()
}//end class
