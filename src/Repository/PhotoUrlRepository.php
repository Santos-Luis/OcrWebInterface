<?php

namespace App\Repository;

use App\Entity\PhotoUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PhotoUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhotoUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhotoUrl[]    findAll()
 * @method PhotoUrl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoUrlRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PhotoUrl::class);
    }

    // /**
    //  * @return PhotoUrl[] Returns an array of PhotoUrl objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PhotoUrl
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
