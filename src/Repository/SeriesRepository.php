<?php

namespace App\Repository;

use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Series>
 *
 * @method Series|null find($id, $lockMode = null, $lockVersion = null)
 * @method Series|null findOneBy(array $criteria, array $orderBy = null)
 * @method Series[]    findAll()
 * @method Series[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Series::class);
    }

    public function all()
    {
        return $this->createQueryBuilder('serie')
            ->orderBy('serie.id','DESC')
            ->getQuery()
            ->getResult();
    }

    public function add(Series $series, bool $flush = false): void
    {
        $this->getEntityManager()->persist($series);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    private function remove(Series $series, bool $flush = false): void
    {
        $this->getEntityManager()->remove($series);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deleteById(int $id): void
    {
        $series = $this->getEntityManager()->getReference(Series::class, $id);
        $this->remove($series, true);
    }

//    /**
//     * @return Series[] Returns an array of Series objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Series
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
