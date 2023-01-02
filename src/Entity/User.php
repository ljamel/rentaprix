<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: Calcul::class, mappedBy: 'user')]
    private Collection $calculs;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $SubscribeId = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $RegistrationDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $ConnectionDate = null;

    public function __construct()
    {
        $this->calculs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Calcul>
     */
    public function getCalculs(): Collection
    {
        return $this->calculs;
    }

    public function addCalcul(Calcul $calcul): self
    {
        if (!$this->calculs->contains($calcul)) {
            $this->calculs->add($calcul);
            $calcul->addUser($this);
        }

        return $this;
    }

    public function removeCalcul(Calcul $calcul): self
    {
        if ($this->calculs->removeElement($calcul)) {
            $calcul->removeUser($this);
        }

        return $this;
    }

    public function getSubscribeId(): ?string
    {
        return $this->SubscribeId;
    }

    public function setSubscribeId(?string $SubscribeId): self
    {
        $this->SubscribeId = $SubscribeId;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->RegistrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $RegistrationDate): self
    {
        $this->RegistrationDate = $RegistrationDate;

        return $this;
    }

    public function getConnectionDate(): ?\DateTimeInterface
    {
        return $this->ConnectionDate;
    }

    public function setConnectionDate(\DateTimeInterface $ConnectionDate): self
    {
        $this->ConnectionDate = $ConnectionDate;

        return $this;
    }
}
