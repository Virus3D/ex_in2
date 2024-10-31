<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TransferRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransferRepository::class)]
class Transfer
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(inversedBy: 'transfersOut')]
    private ?Card $cardOut = null;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(inversedBy: 'transfersIn')]
    private ?Card $cardIn = null;

    #[ORM\Column]
    private ?DateTime $date = null;

    #[ORM\Column]
    private int $balance = 0;

    public function getId(): ?int
    {
        return $this->id;
    }//end getId()

    public function getCardOut(): ?Card
    {
        return $this->cardOut;
    }//end getCardOut()

    public function setCardOut(?Card $cardOut): static
    {
        $this->cardOut = $cardOut;

        return $this;
    }//end setCardOut()

    public function getCardIn(): ?Card
    {
        return $this->cardIn;
    }//end getCardIn()

    public function setCardIn(?Card $cardIn): static
    {
        $this->cardIn = $cardIn;

        return $this;
    }//end setCardIn()

    public function getDate(): ?DateTime
    {
        return $this->date;
    }//end getDate()

    public function setDate(DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }//end setDate()

    public function getBalance(): int
    {
        return $this->balance;
    }//end getBalance()

    public function setBalance(int $balance): static
    {
        $this->balance = $balance;

        return $this;
    }//end setBalance()
}//end class
