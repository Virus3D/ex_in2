<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SpendRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'fns_receipt_key', columns: ['fns_receipt_key']),
    ]
)]

#[ORM\Entity(repositoryClass: SpendRepository::class)]
class Spend
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(inversedBy: 'spends')]
    private ?Card $card = null;

    #[ORM\Column]
    private ?DateTime $date = null;

    #[ORM\Column]
    private int $balance = 0;

    #[ORM\Column(length: 100)]
    private ?string $comment = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne]
    private ?ProductCategory $category = null;

    /**
     * Ключ чека из ФНС API (для связи с импортированными чеками).
     */
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $fnsReceiptKey = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(inversedBy: 'spends')]
    private ?FnsReceipt $fnsReceipt = null;

    public function getId(): ?int
    {
        return $this->id;
    }// end getId()

    public function getCard(): ?Card
    {
        return $this->card;
    }// end getCard()

    public function setCard(?Card $card): static
    {
        $this->card = $card;

        return $this;
    }// end setCard()

    public function getDate(): ?DateTime
    {
        return $this->date;
    }// end getDate()

    public function setDate(DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }// end setDate()

    public function getBalance(): int
    {
        return $this->balance;
    }// end getBalance()

    public function setBalance(int $balance): static
    {
        $this->balance = $balance;

        return $this;
    }// end setBalance()

    public function getComment(): ?string
    {
        return $this->comment;
    }// end getComment()

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }// end setComment()

    public function getCategory(): ?ProductCategory
    {
        return $this->category;
    }// end getCategory()

    public function setCategory(?ProductCategory $category): static
    {
        $this->category = $category;

        return $this;
    }// end setCategory()

    public function getFnsReceiptKey(): ?string
    {
        return $this->fnsReceiptKey;
    }// end getFnsReceiptKey()

    public function setFnsReceiptKey(?string $fnsReceiptKey): static
    {
        $this->fnsReceiptKey = $fnsReceiptKey;

        return $this;
    }// end setFnsReceiptKey()

    public function getFnsReceipt(): ?FnsReceipt
    {
        return $this->fnsReceipt;
    }// end getFnsReceipt()

    public function setFnsReceipt(?FnsReceipt $fnsReceipt): static
    {
        $this->fnsReceipt = $fnsReceipt;

        return $this;
    }// end setFnsReceipt()
}// end class
