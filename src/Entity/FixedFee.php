<?php

namespace App\Entity;

use App\Repository\FixedFeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FixedFeeRepository::class)]
class FixedFee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

   // #[ORM\ManyToMany(targetEntity: Calcul::class, inversedBy: 'fixedFees')]
    //private Collection $calcul;

    #[ORM\OneToMany(mappedBy: 'fixedFee', targetEntity: FixedFeeCalcul::class, orphanRemoval: true)]
    private Collection $fixedFeeCalculs;

    public function __construct()
    {
        //$this->calcul = new ArrayCollection();
        $this->fixedFeeCalculs = new ArrayCollection();
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

    /**
     * @return Collection<int, Calcul>
     */
    //public function getCalcul(): Collection
    //{
      //  return $this->calcul;
    //}

    //public function addCalcul(Calcul $calcul): self
    //{
      //  if (!$this->calcul->contains($calcul)) {
        //    $this->calcul->add($calcul);
        //}

        //return $this;
    //}

    /*public function removeCalcul(Calcul $calcul): self
    {
        $this->calcul->removeElement($calcul);

        return $this;
    }*/

    /**
     * @return Collection<int, FixedFeeCalcul>
     */
    public function getFixedFeeCalculs(): Collection
    {
        return $this->fixedFeeCalculs;
    }

    public function addFixedFeeCalcul(FixedFeeCalcul $fixedFeeCalcul): self
    {
        if (!$this->fixedFeeCalculs->contains($fixedFeeCalcul)) {
            $this->fixedFeeCalculs->add($fixedFeeCalcul);
            $fixedFeeCalcul->setFixedFee($this);
        }

        return $this;
    }

    public function removeFixedFeeCalcul(FixedFeeCalcul $fixedFeeCalcul): self
    {
        if ($this->fixedFeeCalculs->removeElement($fixedFeeCalcul)) {
            // set the owning side to null (unless already changed)
            if ($fixedFeeCalcul->getFixedFee() === $this) {
                $fixedFeeCalcul->setFixedFee(null);
            }
        }

        return $this;
    }
}
