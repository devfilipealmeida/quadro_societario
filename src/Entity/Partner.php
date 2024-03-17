<?php

namespace App\Entity;

use App\Repository\PartnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartnerRepository::class)]
#[ORM\Table(name: 'partners')]
class Partner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 11)]
    private ?string $cpf = null;

    #[ORM\Column(length: 255)]
    private ?string $qualification = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $entry = null;

    #[ORM\ManyToOne(inversedBy: 'partners')]
    private ?Corporation $corporation = null;

    #[ORM\OneToMany(targetEntity: Corporation::class, mappedBy: 'partner')]
    private Collection $corporations;

    public function __construct()
    {
        $this->corporations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf): static
    {
        $this->cpf = $cpf;

        return $this;
    }

    public function getQualification(): ?string
    {
        return $this->qualification;
    }

    public function setQualification(string $qualification): static
    {
        $this->qualification = $qualification;

        return $this;
    }

    public function getEntry(): ?\DateTimeImmutable
    {
        return $this->entry;
    }

    public function setEntry(\DateTimeImmutable $entry): static
    {
        $this->entry = $entry;

        return $this;
    }

    public function getCorporation(): ?Corporation
    {
        return $this->corporation;
    }

    public function setCorporation(?Corporation $corporation): static
    {
        $this->corporation = $corporation;

        return $this;
    }

    public function getCorporations(): Collection
    {
        return $this->corporations;
    }

    public function addCorporation(Corporation $corporation): static
    {
        if (!$this->corporations->contains($corporation)) {
            $this->corporations->add($corporation);
            $corporation->setPartner($this);
        }

        return $this;
    }

    public function removeCorporation(Corporation $corporation): static
    {
        if ($this->corporations->removeElement($corporation)) {
            if ($corporation->getPartner() === $this) {
                $corporation->setPartner(null);
            }
        }

        return $this;
    }
}
