<?php

namespace App\Repository;

use App\Entity\Retailer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Retailer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Retailer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Retailer[]    findAll()
 * @method Retailer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RetailerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Retailer::class);
    }

}
