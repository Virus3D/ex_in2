<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ServicePaymentRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServicePaymentRepository::class)]
class ServicePayment
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne]
    private ?Service $service = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column]
    private ?int $month = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $date = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne]
    private ?Place $place = null;

    private ?Card $card = null;

    public function getId(): ?int
    {
        return $this->id;
    }//end getId()

    public function getService(): ?Service
    {
        return $this->service;
    }//end getService()

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }//end setService()

    public function getYear(): ?int
    {
        return $this->year;
    }//end getYear()

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }//end setYear()

    public function getMonth(): ?int
    {
        return $this->month;
    }//end getMonth()

    public function setMonth(int $month): static
    {
        $this->month = $month;

        return $this;
    }//end setMonth()

    public function getAmount(): ?int
    {
        return $this->amount;
    }//end getAmount()

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }//end setAmount()

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }//end getDate()

    public function setDate(DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }//end setDate()

    public function getPlace(): ?Place
    {
        return $this->place;
    }//end getPlace()

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }//end setPlace()

    /**
     * Get the value of card.
     */
    public function getCard(): ?Card
    {
        return $this->card;
    }//end getCard()

    /**
     * Set the value of card.
     */
    public function setCard(Card $card): self
    {
        $this->card = $card;

        return $this;
    }//end setCard()
}//end class
