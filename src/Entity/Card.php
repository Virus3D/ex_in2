<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(inversedBy: 'cards')]
    private ?CardCategory $category = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\Column]
    private int $balance = 0;

    /** @var Collection<int, Receipt> */
    #[ORM\OneToMany(targetEntity: Receipt::class, mappedBy: 'card')]
    private Collection $receipts;

    /** @var Collection<int, Spend> */
    #[ORM\OneToMany(targetEntity: Spend::class, mappedBy: 'card')]
    private Collection $spends;

    /** @var Collection<int, Transfer> */
    #[ORM\OneToMany(targetEntity: Transfer::class, mappedBy: 'cardOut')]
    private Collection $transfersOut;

    /** @var Collection<int, Transfer> */
    #[ORM\OneToMany(targetEntity: Transfer::class, mappedBy: 'cardIn')]
    private Collection $transfersIn;

    private int $totalReceipt = 0;

    private int $totalSpend = 0;

    public function __construct()
    {
        $this->receipts     = new ArrayCollection();
        $this->spends       = new ArrayCollection();
        $this->transfersOut = new ArrayCollection();
        $this->transfersIn  = new ArrayCollection();
    }//end __construct()

    public function getId(): ?int
    {
        return $this->id;
    }//end getId()

    public function getCategory(): ?CardCategory
    {
        return $this->category;
    }//end getCategory()

    public function setCategory(?CardCategory $category): static
    {
        $this->category = $category;

        return $this;
    }//end setCategory()

    public function getName(): ?string
    {
        return $this->name;
    }//end getName()

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }//end setName()

    public function getType(): ?int
    {
        return $this->type;
    }//end getType()

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }//end setType()

    public function getBalance(): int
    {
        return $this->balance;
    }//end getBalance()

    public function setBalance(int $balance): static
    {
        $this->balance = $balance;

        return $this;
    }//end setBalance()

    /**
     * @return Collection<int, Receipt>
     */
    public function getReceipts(): Collection
    {
        return $this->receipts;
    }//end getReceipts()

    public function addReceipt(Receipt $receipt): static
    {
        if (! $this->receipts->contains($receipt))
        {
            $this->receipts->add($receipt);
            $receipt->setCard($this);
        }

        return $this;
    }//end addReceipt()

    public function removeReceipt(Receipt $receipt): static
    {
        $this->receipts->removeElement($receipt);

        return $this;
    }//end removeReceipt()

    /**
     * @return Collection<int, Spend>
     */
    public function getSpends(): Collection
    {
        return $this->spends;
    }//end getSpends()

    public function addSpend(Spend $spend): static
    {
        if (! $this->spends->contains($spend))
        {
            $this->spends->add($spend);
            $spend->setCard($this);
        }

        return $this;
    }//end addSpend()

    public function removeSpend(Spend $spend): static
    {
        $this->spends->removeElement($spend);

        return $this;
    }//end removeSpend()

    /**
     * @return Collection<int, Transfer>
     */
    public function getTransfersOut(): Collection
    {
        return $this->transfersOut;
    }//end getTransfersOut()

    public function addTransfersOut(Transfer $transfersOut): static
    {
        if (! $this->transfersOut->contains($transfersOut))
        {
            $this->transfersOut->add($transfersOut);
            $transfersOut->setCardOut($this);
        }

        return $this;
    }//end addTransfersOut()

    public function removeTransfersOut(Transfer $transfersOut): static
    {
        $this->transfersOut->removeElement($transfersOut);

        return $this;
    }//end removeTransfersOut()

    /**
     * @return Collection<int, Transfer>
     */
    public function getTransfersIn(): Collection
    {
        return $this->transfersIn;
    }//end getTransfersIn()

    public function addTransfersIn(Transfer $transfersIn): static
    {
        if (! $this->transfersIn->contains($transfersIn))
        {
            $this->transfersIn->add($transfersIn);
            $transfersIn->setCardIn($this);
        }

        return $this;
    }//end addTransfersIn()

    public function removeTransfersIn(Transfer $transfersIn): static
    {
        $this->transfersIn->removeElement($transfersIn);

        return $this;
    }//end removeTransfersIn()

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
}//end class
