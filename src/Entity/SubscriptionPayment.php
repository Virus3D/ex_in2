<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SubscriptionPaymentRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionPaymentRepository::class)]
class SubscriptionPayment
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne]
    private ?Subscription $subscrip = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(inversedBy: 'spends')]
    private ?Card $card = null;

    public function getId(): ?int
    {
        return $this->id;
    }//end getId()

    public function getSubscrip(): ?Subscription
    {
        return $this->subscrip;
    }//end getSubscrip()

    public function setSubscrip(?Subscription $subscrip): static
    {
        $this->subscrip = $subscrip;

        return $this;
    }//end setSubscrip()

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }//end getDate()

    public function setDate(DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }//end setDate()

    public function getAmount(): ?int
    {
        return $this->amount;
    }//end getAmount()

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }//end setAmount()

    public function getCard(): ?Card
    {
        return $this->card;
    }//end getCard()

    public function setCard(?Card $card): static
    {
        $this->card = $card;

        return $this;
    }//end setCard()
}//end class
