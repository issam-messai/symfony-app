<?php

namespace App\Service;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Repository\OrderRepository;
use App\Entity\Order;



class ReportService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }


    public function generate(int $userId): array
    {   
        $orderRepo = $this->em->getRepository(Order::class);

        return $orderRepo->findByCustomer($userId);
    }
}
