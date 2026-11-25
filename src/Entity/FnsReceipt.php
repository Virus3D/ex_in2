<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FnsReceiptRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FnsReceiptRepository::class)]
class FnsReceipt
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    /**
     * Уникальный ключ чека из ФНС API.
     */
    #[ORM\Column(length: 64, unique: true)]
    private ?string $receiptKey = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $kktOwner = null;

    #[ORM\Column]
    private ?DateTime $fiscalDate = null;

    #[ORM\Column]
    private ?int $totalSum = null;

    /**
     * Элементы чека.
     *
     * @var Collection<int, FnsReceiptItem>
     */
    #[ORM\OneToMany(
        targetEntity: FnsReceiptItem::class,
        mappedBy: 'receipt',
        cascade: [
            'persist',
            'remove',
        ],
        orphanRemoval: true
    )]
    private Collection $items;

    /**
     * Расходы, связанные с этим чеком.
     *
     * @var Collection<int, Spend>
     */
    #[ORM\OneToMany(targetEntity: Spend::class, mappedBy: 'fnsReceipt')]
    private Collection $spends;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->spends = new ArrayCollection();
    }// end __construct()

    public function getId(): ?int
    {
        return $this->id;
    }// end getId()

    public function getReceiptKey(): ?string
    {
        return $this->receiptKey;
    }// end getReceiptKey()

    public function setReceiptKey(string $receiptKey): static
    {
        $this->receiptKey = $receiptKey;

        return $this;
    }// end setReceiptKey()

    public function getKktOwner(): ?string
    {
        return $this->kktOwner;
    }// end getKktOwner()

    public function setKktOwner(?string $kktOwner): static
    {
        $this->kktOwner = $kktOwner;

        return $this;
    }// end setKktOwner()

    public function getFiscalDate(): ?DateTime
    {
        return $this->fiscalDate;
    }// end getFiscalDate()

    public function setFiscalDate(DateTime $fiscalDate): static
    {
        $this->fiscalDate = $fiscalDate;

        return $this;
    }// end setFiscalDate()

    public function getTotalSum(): ?int
    {
        return $this->totalSum;
    }// end getTotalSum()

    public function setTotalSum(int $totalSum): static
    {
        $this->totalSum = $totalSum;

        return $this;
    }// end setTotalSum()

    /**
     * @return Collection<int, FnsReceiptItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }// end getItems()

    /**
     * Добавить элемент чека.
     */
    public function addItem(FnsReceiptItem $item): static
    {
        if (! $this->items->contains($item)) {
            $this->items->add($item);
            $item->setReceipt($this);
        }

        return $this;
    }// end addItem()

    /**
     * Удалить элемент чека.
     */
    public function removeItem(FnsReceiptItem $item): static
    {
        if ($this->items->removeElement($item)) {
            $item->setReceipt(null);
        }

        return $this;
    }// end removeItem()

    /**
     * @return Collection<int, Spend>
     */
    public function getSpends(): Collection
    {
        return $this->spends;
    }// end getSpends()
}// end class
