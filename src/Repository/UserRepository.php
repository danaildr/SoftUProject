<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Find all users with their roles eagerly loaded to avoid N+1 queries
     *
     * @return User[]
     */
    public function findAllWithRoles(): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.userRoles', 'r')
            ->addSelect('r')
            ->orderBy('u.fullName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find users by role name with roles eagerly loaded
     *
     * @param string $roleName
     * @return User[]
     */
    public function findByRoleName(string $roleName): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.userRoles', 'r')
            ->addSelect('r')
            ->where('r.name = :roleName')
            ->setParameter('roleName', $roleName)
            ->orderBy('u.fullName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}