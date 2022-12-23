<?php

namespace App\Repository;

use App\Entity\Calcul;
use App\Entity\FixedFee;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join as ExprJoin;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Calcul>
 *
 * @method Calcul|null find($id, $lockMode = null, $lockVersion = null)
 * @method Calcul|null findOneBy(array $criteria, array $orderBy = null)
 * @method Calcul[]    findAll()
 * @method Calcul[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalculRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calcul::class);
    }

    public function save(Calcul $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Calcul $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findCalculsByUserPaginated(int $page, int $limit = 6, int $userId): array 
    {
        $limit = abs($limit);

        $result = [];

        $query = $this->getEntityManager() ->createQueryBuilder()
                        ->select('c')
                        ->from('App\Entity\Calcul', 'c')
                        ->join('c.user', 'u')
                        ->where('u.id = :id')
                        ->setParameter('id', $userId)
                        ->setMaxResults($limit)
                        ->setFirstResult(($limit * $page) - $limit)
                        ->orderBy('c.date', 'DESC');

        $paginator = new Paginator($query);
        
        $data = $paginator->getQuery()->getResult();
        if(empty($data)) {
            return $result;
        }
        
        $pages = ceil($paginator->count() / $limit);
        
        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
    }

    public function findCalculsByUser(int $userId): array
    {
        $query = $this->getEntityManager() ->createQueryBuilder()
            ->select('c')
            ->from('App\Entity\Calcul', 'c')
            ->join('c.user', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $userId);

        return $query->getQuery()->getResult();
    }

    /**
     * returns number of calculs per month
     * @return void
     */
    public function countByMonth(int $userId): array {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT SUBSTRING(c.date, 6, 2) as dateCalcul, COUNT(c) as count
            FROM App\Entity\Calcul c
            INNER JOIN c.user u
            WHERE u.id = :id
            GROUP BY dateCalcul'
        )->setParameter('id', $userId);

        return $query->getResult();

    }
//    /**
//     * @return Calcul[] Returns an array of Calcul objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Calcul
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
