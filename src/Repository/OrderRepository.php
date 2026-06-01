<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findByCustomer(int $customerId): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.customerId = :id')
            ->setParameter('id', $customerId)
            ->orderBy('o.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
