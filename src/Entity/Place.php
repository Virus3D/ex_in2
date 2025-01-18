<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
class Place
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 100)]
    private string $name = '';

    /** @var Collection<int, Service> */
    #[ORM\ManyToMany(targetEntity: Service::class)]
    private Collection $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
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

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }//end getServices()

    public function addService(Service $service): static
    {
        if (! $this->services->contains($service))
        {
            $this->services->add($service);
        }

        return $this;
    }//end addService()

    public function removeService(Service $service): static
    {
        $this->services->removeElement($service);

        return $this;
    }//end removeService()

    public function __toString(): string
    {
        return $this->name;
    }//end __toString()
}//end class
