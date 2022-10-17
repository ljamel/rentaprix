<?php

namespace App\Entity;

use App\Repository\SalaryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalaryRepository::class)]
class Salary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $FullName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $post = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $pay = null;

    #[ORM\ManyToMany(targetEntity: Calcul::class, inversedBy: 'salaries')]
    private Collection $calcul;

    public function __construct()
    {
        $this->calcul = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->FullName;
    }

    public function setFullName(string $FullName): self
    {
        $this->FullName = $FullName;

        return $this;
    }

    public function getPost(): ?string
    {
        return $this->post;
    }

    public function setPost(?string $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getPay(): ?string
    {
        return $this->pay;
    }

    public function setPay(string $pay): self
    {
        $this->pay = $pay;

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
