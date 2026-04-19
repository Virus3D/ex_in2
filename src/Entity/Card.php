<?php

/**
 * Expenses/Income.
 *
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

    /**
     * Поступления.
     *
     * @var Collection<int, Receipt>
     */
    #[ORM\OneToMany(targetEntity: Receipt::class, mappedBy: 'card')]
    private Collection $receipts;

    /**
     * Рвсходы.
     *
     * @var Collection<int, Spend>
     */
    #[ORM\OneToMany(targetEntity: Spend::class, mappedBy: 'card')]
    private Collection $spends;

    /**
     * Транзакции.
     *
     * @var Collection<int, Transfer>
     */
    #[ORM\OneToMany(targetEntity: Transfer::class, mappedBy: 'cardOut')]
    private Collection $transfersOut;

    /**
     * Транзакции.
     *
     * @var Collection<int, Transfer>
     */
    #[ORM\OneToMany(targetEntity: Transfer::class, mappedBy: 'cardIn')]
    private Collection $transfersIn;

    private int $totalReceipt = 0;

    private int $totalSpend = 0;

    private int $totalTransferAdd = 0;

    private int $totalTransferSub = 0;

    public function __construct()
    {
        $this->receipts     = new ArrayCollection();
        $this->spends       = new ArrayCollection();
        $this->transfersOut = new ArrayCollection();
        $this->transfersIn  = new ArrayCollection();
    }// end __construct()

    /**
     * Get the value of Id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }// end getId()

    /**
     * Get the value of category.
     */
    public function getCategory(): ?CardCategory
    {
        return $this->category;
    }// end getCategory()

    /**
     * Set the value of category.
     */
    public function setCategory(?CardCategory $category): static
    {
        $this->category = $category;

        return $this;
    }// end setCategory()

    /**
     * Get the value of name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }// end getName()

    /**
     * Set the value of name.
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }// end setName()

    /**
     * Get the value of type.
     */
    public function getType(): ?int
    {
        return $this->type;
    }// end getType()

    /**
     * Set the value of type.
     */
    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }// end setType()

    /**
     * Get the value of balance.
     */
    public function getBalance(): int
    {
        return $this->balance;
    }// end getBalance()

    /**
     * Set the value of balance.
     */
    public function setBalance(int $balance): static
    {
        $this->balance = $balance;

        return $this;
    }// end setBalance()

    /**
     * Поступления.
     *
     * @return Collection<int, Receipt>
     */
    public function getReceipts(): Collection
    {
        return $this->receipts;
    }// end getReceipts()

    /**
     * Добавить постиупление.
     */
    public function addReceipt(Receipt $receipt): static
    {
        if (! $this->receipts->contains($receipt)) {
            $this->receipts->add($receipt);
            $receipt->setCard($this);
        }

        return $this;
    }// end addReceipt()

    /**
     * Удалить поступление.
     */
    public function removeReceipt(Receipt $receipt): static
    {
        $this->receipts->removeElement($receipt);

        return $this;
    }// end removeReceipt()

    /**
     * Расходы.
     *
     * @return Collection<int, Spend>
     */
    public function getSpends(): Collection
    {
        return $this->spends;
    }// end getSpends()

    /**
     * Добавить расход.
     */
    public function addSpend(Spend $spend): static
    {
        if (! $this->spends->contains($spend)) {
            $this->spends->add($spend);
            $spend->setCard($this);
        }

        return $this;
    }// end addSpend()

    /**
     * Удалить расход.
     */
    public function removeSpend(Spend $spend): static
    {
        $this->spends->removeElement($spend);

        return $this;
    }// end removeSpend()

    /**
     * Траннннзакции.
     *
     * @return Collection<int, Transfer>
     */
    public function getTransfersOut(): Collection
    {
        return $this->transfersOut;
    }// end getTransfersOut()

    /**
     * Добавить транзакцию.
     */
    public function addTransfersOut(Transfer $transfersOut): static
    {
        if (! $this->transfersOut->contains($transfersOut)) {
            $this->transfersOut->add($transfersOut);
            $transfersOut->setCardOut($this);
        }

        return $this;
    }// end addTransfersOut()

    /**
     * Удалить транзакцию.
     */
    public function removeTransfersOut(Transfer $transfersOut): static
    {
        $this->transfersOut->removeElement($transfersOut);

        return $this;
    }// end removeTransfersOut()

    /**
     * Траннннзакции.
     *
     * @return Collection<int, Transfer>
     */
    public function getTransfersIn(): Collection
    {
        return $this->transfersIn;
    }// end getTransfersIn()

    /**
     * Добавить транзакцию.
     */
    public function addTransfersIn(Transfer $transfersIn): static
    {
        if (! $this->transfersIn->contains($transfersIn)) {
            $this->transfersIn->add($transfersIn);
            $transfersIn->setCardIn($this);
        }

        return $this;
    }// end addTransfersIn()

    /**
     * Удалить транзакцию.
     */
    public function removeTransfersIn(Transfer $transfersIn): static
    {
        $this->transfersIn->removeElement($transfersIn);

        return $this;
    }// end removeTransfersIn()

    /**
     * Get the value of totalReceipt.
     */
    public function getTotalReceipt(): int
    {
        return $this->totalReceipt;
    }// end getTotalReceipt()

    /**
     * Set the value of totalReceipt.
     */
    public function setTotalReceipt(int $totalReceipt): self
    {
        $this->totalReceipt = $totalReceipt;

        return $this;
    }// end setTotalReceipt()

    /**
     * Add the value of totalReceipt.
     */
    public function addTotalReceipt(int $receipt): self
    {
        $this->totalReceipt += $receipt;

        return $this;
    }// end addTotalReceipt()

    /**
     * Get the value of totalSpend.
     */
    public function getTotalSpend(): int
    {
        return $this->totalSpend;
    }// end getTotalSpend()

    /**
     * Set the value of totalSpend.
     */
    public function setTotalSpend(int $totalSpend): self
    {
        $this->totalSpend = $totalSpend;

        return $this;
    }// end setTotalSpend()

    /**
     * Add the value of totalSpend.
     */
    public function addTotalSpend(int $spend): self
    {
        $this->totalSpend += $spend;

        return $this;
    }// end addTotalSpend()

    /**
     * Get the value of totalTransferAdd.
     */
    public function getTotalTransferAdd(): int
    {
        return $this->totalTransferAdd;
    }// end getTotalTransferAdd()

    /**
     * Set the value of totalTransferAdd.
     */
    public function setTotalTransferAdd(int $totalTransferAdd): self
    {
        $this->totalTransferAdd = $totalTransferAdd;

        return $this;
    }// end setTotalTransferAdd()

    /**
     * Add the value of totalTransferAdd.
     */
    public function addTotalTransferAdd(int $transferAdd): self
    {
        $this->totalTransferAdd += $transferAdd;

        return $this;
    }// end addTotalTransferAdd()

    /**
     * Get the value of totalTransferSub.
     */
    public function getTotalTransferSub(): int
    {
        return $this->totalTransferSub;
    }// end getTotalTransferSub()

    /**
     * Set the value of totalTransferSub.
     */
    public function setTotalTransferSub(int $totalTransferSub): self
    {
        $this->totalTransferSub = $totalTransferSub;

        return $this;
    }// end setTotalTransferSub()

    /**
     * Add the value of totalTransferSub.
     */
    public function addTotalTransferSub(int $transferSub): self
    {
        $this->totalTransferSub += $transferSub;

        return $this;
    }// end addTotalTransferSub()
}// end class
