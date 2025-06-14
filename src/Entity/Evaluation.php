<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
#[ORM\Table(name: 'evaluations')]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank(message: 'Оценката е задължителна.')]
    #[Assert\Range(min: 2, max: 6, notInRangeMessage: 'Оценката трябва да бъде между {{ min }} и {{ max }}.')]
    private ?float $value = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Коментарът е задължителен.')]
    #[Assert\Length(min: 5, minMessage: 'Коментарът трябва да бъде поне {{ limit }} символа.')]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAdded = null;

    #[ORM\Column]
    private ?int $authorId = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'evaluations')]
    #[ORM\JoinColumn(name: 'authorId', referencedColumnName: 'id')]
    private ?User $teacher = null;

    #[ORM\Column]
    private ?int $recipient = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'receivedEvaluations')]
    #[ORM\JoinColumn(name: 'recepientId', referencedColumnName: 'id')]
    private ?User $student = null;

    #[ORM\Column]
    private ?int $courseid = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'evaluations')]
    #[ORM\JoinColumn(name: 'coursesId', referencedColumnName: 'id')]
    private ?Course $course = null;

    public function __construct()
    {
        $this->dateAdded = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->dateAdded;
    }

    public function setDateAdded(\DateTimeInterface $dateAdded): static
    {
        $this->dateAdded = $dateAdded;
        return $this;
    }

    public function getAuthorId(): ?int
    {
        return $this->authorId;
    }

    public function setAuthorId(int $authorId): static
    {
        $this->authorId = $authorId;
        return $this;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): static
    {
        $this->teacher = $teacher;
        return $this;
    }

    public function getRecipient(): ?int
    {
        return $this->recipient;
    }

    public function setRecipient(int $recipient): static
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): static
    {
        $this->student = $student;
        return $this;
    }

    public function getCourseid(): ?int
    {
        return $this->courseid;
    }

    public function setCourseid(int $courseid): static
    {
        $this->courseid = $courseid;
        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;
        return $this;
    }
}
