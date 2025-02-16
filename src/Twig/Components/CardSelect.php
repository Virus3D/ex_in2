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
use App\Repository\CardRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class CardSelect
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true, onUpdated: 'onUpdatedSelectedCardId')]
    public ?int $selectedCardId = null;

    #[LiveProp]
    public ?Card $selectedCard = null;

    #[LiveProp]
    public string $field = '';

    public function __construct(private CardRepository $cardRepository, private RequestStack $requestStack)
    {}//end __construct()

    /**
     * Возвращает список карт.
     *
     * @return array<string, Card[]>
     */
    public function getCards(): array
    {
        return $this->groupBy($this->cardRepository->findAll());
    }//end getCards()

    /**
     * Группировка.
     *
     * @param array<int, Card> $items
     *
     * @return array<string, Card[]>
     */
    public function groupBy(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $groupKey = $item->getCategory()->getName();
            if ($groupKey) {
                $result[$groupKey][] = $item;
            }
        }

        return $result;
    }//end groupBy()

    /**
     * Обновление выбранной карты.
     */
    public function onUpdatedSelectedCardId(): void
    {
        $this->selectedCard = ($this->selectedCardId) ? $this->cardRepository->find($this->selectedCardId) : null;
        if ($this->field) {
            $session = $this->requestStack->getCurrentRequest()->getSession();
            $session->set($this->field, $this->selectedCardId);
            $this->emitUp('updateCard');
        }
    }//end onUpdatedSelectedCardId()
}//end class
