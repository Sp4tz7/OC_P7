<?php

namespace App\Repository;

use App\Entity\Product;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function search($term, $order = 'asc', $limit = 20, $offset = 0)
    {
        $query = $this
            ->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.name', $order);

        if ($term) {
            $query
                ->where('a.name LIKE ?1')
                ->setParameter(1, '%'.$term.'%');
        }

        return $this->paginate($query, $limit, $offset);
    }

}
