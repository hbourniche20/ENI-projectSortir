<?php

namespace App\Repository;

use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Site|null find($id, $lockMode = null, $lockVersion = null)
 * @method Site|null findOneBy(array $criteria, array $orderBy = null)
 * @method Site[]    findAll()
 * @method Site[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Site::class);
    }

    public function findByVille($idVille)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.ville = :id')
            ->setParameter('id', $idVille)
            ->orderBy('s.nom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLikeNameSite($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :val')
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllWithVille()
    {
        return $this->createQueryBuilder('s')
            ->innerJoin("s.ville",'v')
            ->orderBy('v.nom', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}
