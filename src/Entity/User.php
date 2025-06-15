<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Този email адрес вече се използва.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Email адресът е задължителен.')]
    #[Assert\Email(message: 'Моля въведете валиден email адрес.')]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Паролата е задължителна.', groups: ['registration'])]
    #[Assert\Length(min: 6, minMessage: 'Паролата трябва да бъде поне {{ limit }} символа.', groups: ['registration', 'password_change'])]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Пълното име е задължително.')]
    #[Assert\Length(min: 2, minMessage: 'Пълното име трябва да бъде поне {{ limit }} символа.')]
    private ?string $fullName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\Column(length: 5, nullable: false)]
    private string $locale = 'bg';

    #[ORM\OneToMany(targetEntity: Evaluation::class, mappedBy: 'teacher')]
    private Collection $evaluations;

    #[ORM\OneToMany(targetEntity: Evaluation::class, mappedBy: 'student')]
    private Collection $receivedEvaluations;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'users_roles')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'role_id', referencedColumnName: 'id')]
    private Collection $userRoles;

    public function __construct()
    {
        $this->evaluations = new ArrayCollection();
        $this->receivedEvaluations = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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
        $stringRoles = [];
        foreach ($this->userRoles as $role) {
            $stringRoles[] = 'ROLE_' . strtoupper($role->getName());
        }

        // guarantee every user at least has ROLE_USER
        $stringRoles[] = 'ROLE_USER';

        return array_unique($stringRoles);
    }



    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function isAdmin(): bool
    {
        return in_array('ADMIN', $this->getStringRoles());
    }

    public function isTeacher(): bool
    {
        return in_array('TEACHER', $this->getStringRoles());
    }

    public function isStudent(): bool
    {
        return in_array('STUDENT', $this->getStringRoles());
    }

    /**
     * @return Collection<int, Evaluation>
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): static
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations->add($evaluation);
            $evaluation->setTeacher($this);
        }
        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): static
    {
        if ($this->evaluations->removeElement($evaluation)) {
            if ($evaluation->getTeacher() === $this) {
                $evaluation->setTeacher(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Evaluation>
     */
    public function getReceivedEvaluations(): Collection
    {
        return $this->receivedEvaluations;
    }

    public function addReceivedEvaluation(Evaluation $evaluation): static
    {
        if (!$this->receivedEvaluations->contains($evaluation)) {
            $this->receivedEvaluations->add($evaluation);
            $evaluation->setStudent($this);
        }
        return $this;
    }

    public function removeReceivedEvaluation(Evaluation $evaluation): static
    {
        if ($this->receivedEvaluations->removeElement($evaluation)) {
            if ($evaluation->getStudent() === $this) {
                $evaluation->setStudent(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $role): static
    {
        if (!$this->userRoles->contains($role)) {
            $this->userRoles->add($role);
            $role->addUser($this);
        }
        return $this;
    }

    public function removeUserRole(Role $role): static
    {
        if ($this->userRoles->removeElement($role)) {
            $role->removeUser($this);
        }
        return $this;
    }

    public function getStringRoles(): array
    {
        $stringRoles = [];
        foreach ($this->userRoles as $role) {
            $stringRoles[] = $role->getName();
        }
        return $stringRoles;
    }

    /**
     * Метод за съвместимост със старите темплейти
     * Връща масив с имената на ролите
     */
    public function Roles(): array
    {
        return $this->getStringRoles();
    }
}