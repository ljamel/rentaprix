<?php

namespace App\Repository;

use App\Entity\Calcul;
use App\Entity\FixedFee;
use App\Entity\Salary;
use App\Entity\User;
use App\Entity\VariableFee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
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

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

        /**
     * @return Calcul[] Returns an array of Calcul objects
     */
    public function findByFeeUser($idUser): array
    {
        // join calcul with user
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $idUser)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;
    }

            /**
     * @return Calcul[] Returns an array of Calcul objects
     */
    public function findByUserOne($idUser, $idFeevrariable): array
    {
        // join calcul with user
        return $this->createQueryBuilder('c')
            ->from(\App\Entity\VariableFee::class, 'u')
            ->andWhere('c.id = :valUser')
            ->setParameter('valUser', $idUser)
            ->andWhere('u.id = :valFee')
            ->setParameter('valFee', $idFeevrariable)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getSingleResult()
        ;
    }

    /**
     * @return Product[]
     */
    public function findAllGreaterThanPrice(int $price): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Product p
            WHERE p.price > :price
            ORDER BY p.price ASC'
        )->setParameter('price', $price);

        // returns an array of Product objects
        return $query->getResult();
    }

        /**
     * @return FixedFee[] Returns an array of Calcul objects
     */
    public function findFixedFeesByUser($idUser): array
    {
        // join calcul with user
        return $this->createQueryBuilder('u')
            ->select('f')
            ->from(FixedFee::class, 'f')
            ->innerJoin(Calcul::class, 'c')
            ->andWhere('u.id = :val')
            ->setParameter('val', $idUser)
            ->getQuery()
            ->getResult()
        ;
    }

        /**
     * @return FixedFee[] Returns an array of Calcul objects
     */
    public function findVariableFeesByUser($idUser): array
    {
        // join calcul with user
        return $this->createQueryBuilder('u')
            ->select('f')
            ->from(VariableFee::class, 'f')
            ->innerJoin(Calcul::class, 'c')
            ->andWhere('u.id = :val')
            ->setParameter('val', $idUser)
            ->getQuery()
            ->getResult()
        ;
    }

        /**
     * @return Salary[] Returns an array of Calcul objects
     */
    public function findSalariesByUser($idUser): array
    {
        // join calcul with user
        return $this->createQueryBuilder('u')
            ->select('f')
            ->from(Salary::class, 'f')
            ->innerJoin(Calcul::class, 'c')
            ->andWhere('u.id = :val')
            ->setParameter('val', $idUser)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
