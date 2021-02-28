<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function search($term, $order = 'asc', $limit = 20, $offset = 0, $retailer)
    {

        $query = $this
            ->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.fullname', $order);

        if (null !== $term) {
            $query
                ->where('a.fullname LIKE ?1')
                ->andWhere('a.retailer = ?3')
                ->orWhere('a.email LIKE ?2')
                ->setParameter(1, '%'.$term.'%')
                ->setParameter(2, '%'.$term.'%');
        }
        if (is_object($retailer)) {
            $query->andWhere('a.retailer = ?3')->setParameter(3, $retailer->getId());
        }

        return $this->paginate($query, $limit, $offset);
    }
}
