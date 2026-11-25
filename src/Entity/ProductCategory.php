<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductCategoryRepository::class)]
class ProductCategory
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $name = null;

    /**
     * Ключевые слова для авто-детекта категории.
     */
    #[ORM\Column(length: 500, nullable: true)]
    private ?string $keywords = null;

    public function getId(): ?int
    {
        return $this->id;
    }// end getId()

    public function getName(): ?string
    {
        return $this->name;
    }// end getName()

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }// end setName()

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }// end getKeywords()

    public function setKeywords(?string $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }// end setKeywords()

    public function __toString(): string
    {
        return $this->name ?? '';
    }// end __toString()
}// end class
