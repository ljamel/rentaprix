<?php

namespace App\Repository;

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
     * @return FixedFee[] Returns an array of FixedFee objects
     */
    public function findFixedFeesByUser($idUser): array
    {
            $entityManager = $this->getEntityManager();

            $query = $entityManager->createQuery(
                'SELECT f
                FROM App\Entity\FixedFee f
                INNER JOIN f.calcul c
                INNER JOIN c.user u
                WHERE u.id = :id'
                
            )->setParameter('id', $idUser);
            
            return $query->getResult();
    }

        /**
     * @return FixedFee[] Returns an array of VariableFee objects
     */
    public function findVariableFeesByUser($idUser): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT v
            FROM App\Entity\VariableFee v
            INNER JOIN v.calcul c
            INNER JOIN c.user u
            WHERE u.id = :id'
            
        )->setParameter('id', $idUser);
        
        return $query->getResult();
    }

        /**
     * @return Salary[] Returns an array of Salary objects
     */
    public function findSalariesByUser($idUser): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT s
            FROM App\Entity\Salary s
            INNER JOIN s.calcul c
            INNER JOIN c.user u
            WHERE u.id = :id'
            
        )->setParameter('id', $idUser);
        
        return $query->getResult();
    }

        /**
     * @return User Returns an array of FixedFee objects
     */
    public function findUserByFixedFee($idFixedFee): array
    {
            $entityManager = $this->getEntityManager();

            $query = $entityManager->createQuery(
                'SELECT u
                FROM App\Entity\User u
                INNER JOIN u.calculs c
                INNER JOIN c.fixedFees f
                WHERE f.id = :id'
                
            )->setParameter('id', $idFixedFee);
            
            return $query->getResult();
    }

        /**
     * @return User Returns an array of FixedFee objects
     */
    public function findUserByVariableFee($idFixedFee): array
    {
            $entityManager = $this->getEntityManager();

            $query = $entityManager->createQuery(
                'SELECT u
                FROM App\Entity\User u
                INNER JOIN u.calculs c
                INNER JOIN c.variableFees v
                WHERE v.id = :id'
                
            )->setParameter('id', $idFixedFee);
            
            return $query->getResult();
    }

        /**
     * @return User Returns an array of FixedFee objects
     */
    public function findUserBySalary($idFixedFee): array
    {
            $entityManager = $this->getEntityManager();

            $query = $entityManager->createQuery(
                'SELECT u
                FROM App\Entity\User u
                INNER JOIN u.calculs c
                INNER JOIN c.salaries s
                WHERE s.id = :id'
                
            )->setParameter('id', $idFixedFee);
            
            return $query->getResult();
    }
}
