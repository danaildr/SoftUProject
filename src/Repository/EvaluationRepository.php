<?php

namespace App\Repository;

use App\Entity\Evaluation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evaluation>
 *
 * @method Evaluation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evaluation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evaluation[]    findAll()
 * @method Evaluation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }

    /**
     * Find all evaluations with related entities eagerly loaded to avoid N+1 queries
     *
     * @return Evaluation[]
     */
    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.teacher', 't')
            ->leftJoin('e.student', 's')
            ->leftJoin('e.course', 'c')
            ->addSelect('t', 's', 'c')
            ->orderBy('e.dateAdded', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find evaluations by recipient (student) with relations eagerly loaded
     *
     * @param int $recipientId
     * @param array $orderBy
     * @return Evaluation[]
     */
    public function findByRecipientWithRelations(int $recipientId, array $orderBy = []): array
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.teacher', 't')
            ->leftJoin('e.student', 's')
            ->leftJoin('e.course', 'c')
            ->addSelect('t', 's', 'c')
            ->where('e.recipient = :recipientId')
            ->setParameter('recipientId', $recipientId);

        // Add ordering
        foreach ($orderBy as $field => $direction) {
            $qb->addOrderBy('e.' . $field, $direction);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find evaluations by author (teacher) with relations eagerly loaded
     *
     * @param int $authorId
     * @param array $orderBy
     * @return Evaluation[]
     */
    public function findByAuthorWithRelations(int $authorId, array $orderBy = []): array
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.teacher', 't')
            ->leftJoin('e.student', 's')
            ->leftJoin('e.course', 'c')
            ->addSelect('t', 's', 'c')
            ->where('e.authorId = :authorId')
            ->setParameter('authorId', $authorId);

        // Add ordering
        foreach ($orderBy as $field => $direction) {
            $qb->addOrderBy('e.' . $field, $direction);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find evaluations by course with relations eagerly loaded
     *
     * @param int $courseId
     * @return Evaluation[]
     */
    public function findByCourseWithRelations(int $courseId): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.teacher', 't')
            ->leftJoin('e.student', 's')
            ->leftJoin('e.course', 'c')
            ->addSelect('t', 's', 'c')
            ->where('e.courseid = :courseId')
            ->setParameter('courseId', $courseId)
            ->orderBy('e.dateAdded', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find single evaluation with relations eagerly loaded
     *
     * @param int $id
     * @return Evaluation|null
     */
    public function findOneWithRelations(int $id): ?Evaluation
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.teacher', 't')
            ->leftJoin('e.student', 's')
            ->leftJoin('e.course', 'c')
            ->addSelect('t', 's', 'c')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Evaluation[] Returns an array of Evaluation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Evaluation
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
