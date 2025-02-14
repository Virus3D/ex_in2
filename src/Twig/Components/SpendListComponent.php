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
use App\Helper\FilterDataHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SpendListComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?Card $selectedCard = null;

    public string $field = 'spendCard';

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

        $cardId = $request->getSession()->get($this->field);

        $this->selectedCard = $cardId ? $cardRepository->find($cardId) : null;
    }//end setSelectedCard()

    /**
     * Получить список расходов.
     *
     * @return Spend[]
     */
    public function getSpendList(): array
    {
        $this->setSelectedCard();

        $spendList = $this->entityManager->getRepository(Spend::class)->list(
            FilterDataHelper::$startDate,
            FilterDataHelper::$endDate,
            $this->selectedCard
        );

        $this->total = array_reduce($spendList, static fn ($carry, $spend) => $carry + $spend->getBalance(), 0);

        return $spendList;
    }//end getSpendList()
}//end class
