<?php

namespace App\Entity;

use App\Repository\FixedFeeCalculRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FixedFeeCalculRepository::class)]
class FixedFeeCalcul
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], fetch: "EXTRA_LAZY", inversedBy: 'fixedFeeCalculs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Calcul $calcul = null;

    #[ORM\ManyToOne(cascade: ['persist'], fetch: "EXTRA_LAZY", inversedBy: 'fixedFeeCalculs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FixedFee $fixedFee = null;

    #[ORM\Column]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCalcul(): ?Calcul
    {
        return $this->calcul;
    }

    public function setCalcul(?Calcul $calcul): self
    {
        $this->calcul = $calcul;

        return $this;
    }

    public function getFixedFee(): ?FixedFee
    {
        return $this->fixedFee;
    }

    public function setFixedFee(?FixedFee $fixedFee): self
    {
        $this->fixedFee = $fixedFee;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
