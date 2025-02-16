<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CardCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardCategoryRepository::class)]
class CardCategory
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    /** @var Collection<int, Card> */
    #[ORM\OneToMany(targetEntity: Card::class, mappedBy: 'category')]
    private Collection $cards;

    private int $totalReceipt = 0;

    private int $totalSpend = 0;

    private int $balance = 0;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }//end __construct()

    public function getId(): ?int
    {
        return $this->id;
    }//end getId()

    public function getName(): ?string
    {
        return $this->name;
    }//end getName()

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }//end setName()

    /**
     * @return Collection<int, Card>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }//end getCards()

    public function addCard(Card $card): static
    {
        if (! $this->cards->contains($card)) {
            $this->cards->add($card);
            $card->setCategory($this);
        }

        return $this;
    }//end addCard()

    public function removeCard(Card $card): static
    {
        $this->cards->removeElement($card);

        return $this;
    }//end removeCard()

    public function __toString(): string
    {
        return $this->name;
    }//end __toString()

    /**
     * Get the value of totalReceipt.
     */
    public function getTotalReceipt(): int
    {
        return $this->totalReceipt;
    }//end getTotalReceipt()

    /**
     * Set the value of totalReceipt.
     */
    public function setTotalReceipt(int $totalReceipt): self
    {
        $this->totalReceipt = $totalReceipt;

        return $this;
    }//end setTotalReceipt()

    /**
     *Add the value of totalReceipt.
     */
    public function addTotalReceipt(int $receipt): self
    {
        $this->totalReceipt += $receipt;

        return $this;
    }//end setTotalReceipt()

    /**
     * Get the value of totalSpend.
     */
    public function getTotalSpend(): int
    {
        return $this->totalSpend;
    }//end getTotalSpend()

    /**
     * Set the value of totalSpend.
     */
    public function setTotalSpend(int $totalSpend): self
    {
        $this->totalSpend = $totalSpend;

        return $this;
    }//end setTotalSpend()

    /**
     * Add the value of totalSpend.
     */
    public function addTotalSpend(int $spend): self
    {
        $this->totalSpend += $spend;

        return $this;
    }//end addTotalSpend()

    /**
     * Get the value of balance.
     */
    public function getBalance(): int
    {
        return $this->balance;
    }//end getBalance()

    /**
     * Set the value of balance.
     */
    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }//end setBalance()
}//end class
