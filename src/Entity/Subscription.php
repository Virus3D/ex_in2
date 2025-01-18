<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column]
    private ?int $day = null;

    #[ORM\Column]
    private ?int $month = null;

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

    public function getType(): ?int
    {
        return $this->type;
    }//end getType()

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }//end setType()

    public function getAmount(): ?int
    {
        return $this->amount;
    }//end getAmount()

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }//end setAmount()

    public function getDay(): ?int
    {
        return $this->day;
    }//end getDay()

    public function setDay(int $day): static
    {
        $this->day = $day;

        return $this;
    }//end setDay()

    public function getMonth(): ?int
    {
        return $this->month;
    }//end getMonth()

    public function setMonth(int $month): static
    {
        $this->month = $month;

        return $this;
    }//end setMonth()
}//end class
