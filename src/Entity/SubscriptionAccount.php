<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SubscriptionAccountRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionAccountRepository::class)]
class SubscriptionAccount
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne]
    private ?Subscription $subscrip = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $amount = null;

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
}//end class
