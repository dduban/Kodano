<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Service\ProductService;
use App\Domain\Exception\Product\ProductNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    #[Route('/{id}/notify', name: 'app_product_notify', methods: ['POST'])]
    public function notify(string $id): JsonResponse
    {
        try {
            $this->productService->sendNotifications($id);
            return $this->json(['message' => 'Notifications sent successfully'], Response::HTTP_OK);
        } catch (ProductNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}