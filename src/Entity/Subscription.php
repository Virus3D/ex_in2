<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Period;
use App\Repository\SubscriptionRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\Column(options: ['unsigned' => true])]
    private int $amount;

    #[ORM\Column]
    private int $balance;

    #[ORM\Column(enumType: Period::class)]
    private Period $period;

    #[ORM\Column(name: 'next_payment_date', type: Types::DATE_MUTABLE)]
    private DateTimeInterface $nextPaymentDate;

    public function __construct()
    {
        $this->setBalance(0);
    }//end __construct()

    public function getId(): ?int
    {
        return $this->id;
    }//end getId()

    public function getName(): string
    {
        return $this->name;
    }//end getName()

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }//end setName()

    public function getAmount(): int
    {
        return $this->amount;
    }//end getAmount()

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }//end setAmount()

    public function getBalance(): int
    {
        return $this->balance;
    }//end getBalance()

    public function setBalance(?int $balance): static
    {
        $this->balance = $balance ?? 0;

        return $this;
    }//end setBalance()

    public function getPeriod(): Period
    {
        return $this->period;
    }//end getPeriod()

    public function setPeriod(Period $period): static
    {
        $this->period = $period;

        return $this;
    }//end setPeriod()

    public function getNextPaymentDate(): DateTimeInterface
    {
        return $this->nextPaymentDate;
    }//end getNextPaymentDate()

    public function setNextPaymentDate(DateTimeInterface $nextPaymentDate): static
    {
        $this->nextPaymentDate = $nextPaymentDate;

        return $this;
    }//end setNextPaymentDate()
}//end class
