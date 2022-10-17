<?php

namespace App\Entity;

use App\Repository\VariableFeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VariableFeeRepository::class)]
class VariableFee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    private ?string $unit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\ManyToMany(targetEntity: Calcul::class, inversedBy: 'variableFees')]
    private Collection $calcul;

    public function __construct()
    {
        $this->calcul = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Calcul>
     */
    public function getCalcul(): Collection
    {
        return $this->calcul;
    }

    public function addCalcul(Calcul $calcul): self
    {
        if (!$this->calcul->contains($calcul)) {
            $this->calcul->add($calcul);
        }

        return $this;
    }

    public function removeCalcul(Calcul $calcul): self
    {
        $this->calcul->removeElement($calcul);

        return $this;
    }
}
