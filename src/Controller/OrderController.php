<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Service\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ReportService $reportService,
    ) {
    }

    #[Route('/orders/customer/{id}', name: 'orders_by_customer', methods: ['GET'])]
    public function byCustomer(int $id): JsonResponse
    {
        $orders = $this->orderRepository->findByCustomer($id);

        return $this->json([
            'customer_id' => $id,
            'count'       => count($orders),
            'orders'      => array_map(fn($o) => [
                'id'        => $o->getId(),
                'reference' => $o->getReference(),
                'status'    => $o->getStatus(),
            ], $orders),
        ]);
    }

    #[Route('/report/{userId}', name: 'report', methods: ['GET'])]
    public function report(int $userId): JsonResponse
    {
        $data = $this->reportService->generate($userId);

       return $this->json([
            'user_id' => $userId,
            'rows'    => array_map(fn($o) => [
                'id'        => $o->getId(),
                'reference' => $o->getReference(),
                'status'    => $o->getStatus(),
            ], $data),
        ]);
    }
}
