<?php

namespace SoftUProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Evaluation
 *
 * @ORM\Table(name="evaluations")
 * @ORM\Entity(repositoryClass="SoftUProjectBundle\Repository\EvaluationRepository")
 */
class Evaluation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var int
     *
     * @ORM\Column(name="authorId", type="integer")
     */
    private $authorId;


    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="SoftUProjectBundle\Entity\User", inversedBy="evaluations")
     * @ORM\JoinColumn(name="authorId", referencedColumnName="id")
     *
     */
    private $teacher;

    /**
     * @var int
     *
     * @ORM\Column(name="recepientId", type="integer")
     */
    private $recipient;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="SoftUProjectBundle\Entity\User", inversedBy="reseivedevaluations")
     * @ORM\JoinColumn(name="recepientId", referencedColumnName="id")
     *
     */
    private $student;

    /**
     * @var int
     *
     * @ORM\Column(name="courseId", type="integer")
     */
    private $courseid;

    /**
     * @var Course
     *
     * @ORM\ManyToOne(targetEntity="SoftUProjectBundle\Entity\Course", inversedBy="evaluations")
     * @ORM\JoinColumn(name="coursesId", referencedColumnName="id")
     */
    private $course;

    public function __construct()
    {
        $this->dateAdded = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return Evaluation
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Evaluation
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {

            return $this->comment;


    }

    /**
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param \DateTime $dateAdded
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    }



    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     *
     * @return Evaluation
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
        return $this;
    }

    /**
     * @return User
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * @param User $teacher
     * @return  Evaluation
     */
    public function setTeacher(User $teacher = null)
    {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param int $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * @return User
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * @param User $student
     */
    public function setStudent($student)
    {
        $this->student = $student;
    }

    /**
     * @return Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param Course $course
     */
    public function setCourse($course)
    {
        $this->course = $course;
    }

    /**
     * @return int
     */
    public function getCourseid()
    {
        return $this->courseid;
    }

    /**
     * @param int $courseid
     */
    public function setCourseid($courseid)
    {
        $this->courseid = $courseid;
    }

}

