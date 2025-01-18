<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Period;
use App\Repository\RegularRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegularRepository::class)]
class Regular
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column(enumType: Period::class)]
    private ?Period $period = null;

    #[ORM\Column(name: 'next_payment_date', type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $nextPaymentDate = null;

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

    public function getAmount(): ?int
    {
        return $this->amount;
    }//end getAmount()

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }//end setAmount()

    public function getPeriod(): ?Period
    {
        return $this->period;
    }//end getPeriod()

    public function setPeriod(Period $period): static
    {
        $this->period = $period;

        return $this;
    }//end setPeriod()

    public function getNextPaymentDate(): ?DateTimeInterface
    {
        return $this->nextPaymentDate;
    }//end getNextPaymentDate()

    public function setNextPaymentDate(DateTimeInterface $nextPaymentDate): static
    {
        $this->nextPaymentDate = $nextPaymentDate;

        return $this;
    }//end setNextPaymentDate()
}//end class
