<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FnsReceiptItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FnsReceiptItemRepository::class)]
class FnsReceiptItem
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?FnsReceipt $receipt = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne]
    private ?ProductCategory $category = null;

    #[ORM\Column(length: 300)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $sum = null;

    public function getId(): ?int
    {
        return $this->id;
    }// end getId()

    public function getReceipt(): ?FnsReceipt
    {
        return $this->receipt;
    }// end getReceipt()

    public function setReceipt(?FnsReceipt $receipt): static
    {
        $this->receipt = $receipt;

        return $this;
    }// end setReceipt()

    public function getCategory(): ?ProductCategory
    {
        return $this->category;
    }// end getCategory()

    public function setCategory(?ProductCategory $category): static
    {
        $this->category = $category;

        return $this;
    }// end setCategory()

    public function getName(): ?string
    {
        return $this->name;
    }// end getName()

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }// end setName()

    public function getPrice(): ?int
    {
        return $this->price;
    }// end getPrice()

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }// end setPrice()

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }// end getQuantity()

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }// end setQuantity()

    public function getSum(): ?int
    {
        return $this->sum;
    }// end getSum()

    public function setSum(int $sum): static
    {
        $this->sum = $sum;

        return $this;
    }// end setSum()

    public function __toString(): string
    {
        $category = $this->category !== null ? ' [' . $this->category->getName() . ']' : '';

        return $this->name . $category;
    }// end __toString()
}// end class
