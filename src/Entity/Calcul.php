<?php

namespace App\Entity;

use App\Repository\CalculRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CalculRepository::class)]
class Calcul
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $devis = null;

    #[ORM\Column]
    private ?int $durationMonth = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'calculs')]
    private Collection $user;

    #[ORM\ManyToMany(targetEntity: FixedFee::class, mappedBy: 'calcul', cascade: ['persist'], indexBy: 'id', fetch: "EXTRA_LAZY")]
    private Collection $fixedFees;

    #[ORM\ManyToMany(targetEntity: VariableFee::class, mappedBy: 'calcul', cascade: ['persist'])]
    private Collection $variableFees;

    #[ORM\ManyToMany(targetEntity: Salary::class, mappedBy: 'calcul', cascade: ['persist'])]
    private Collection $salaries;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->fixedFees = new ArrayCollection();
        $this->variableFees = new ArrayCollection();
        $this->salaries = new ArrayCollection();
        $this->date = new DateTime('now');
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDevis(): ?string
    {
        return $this->devis;
    }

    public function setDevis(string $devis): self
    {
        $this->devis = $devis;

        return $this;
    }

    public function getDurationMonth(): ?int
    {
        return $this->durationMonth;
    }

    public function setDurationMonth(int $durationMonth): self
    {
        $this->durationMonth = $durationMonth;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, FixedFee>
     */
    public function getFixedFees(): Collection
    {
        return $this->fixedFees;
    }

    public function addFixedFee(FixedFee $fixedFee): self
    {
        if (!$this->fixedFees->contains($fixedFee)) {
            $this->fixedFees->add($fixedFee);
            $fixedFee->addCalcul($this);
        }

        return $this;
    }

    public function removeFixedFee(FixedFee $fixedFee): self
    {
        if ($this->fixedFees->removeElement($fixedFee)) {
            $fixedFee->removeCalcul($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, VariableFee>
     */
    public function getVariableFees(): Collection
    {
        return $this->variableFees;
    }

    public function addVariableFee(VariableFee $variableFee): self
    {
        if (!$this->variableFees->contains($variableFee)) {
            $this->variableFees->add($variableFee);
            $variableFee->addCalcul($this);
        }

        return $this;
    }

    public function removeVariableFee(VariableFee $variableFee): self
    {
        if ($this->variableFees->removeElement($variableFee)) {
            $variableFee->removeCalcul($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Salary>
     */
    public function getSalaries(): Collection
    {
        return $this->salaries;
    }

    public function addSalary(Salary $salary): self
    {
        if (!$this->salaries->contains($salary)) {
            $this->salaries->add($salary);
            $salary->addCalcul($this);
        }

        return $this;
    }

    public function removeSalary(Salary $salary): self
    {
        if ($this->salaries->removeElement($salary)) {
            $salary->removeCalcul($this);
        }

        return $this;
    }
}
