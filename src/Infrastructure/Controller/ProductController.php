<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Service\ProductService;
use App\Domain\Exception\Category\CategoryNotFoundException;
use App\Domain\Exception\Product\ProductNotFoundException;
use App\Domain\Exception\Product\ProductValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    #[Route('', name: 'app_product_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $products = $this->productService->findAll();
        return $this->json($products, Response::HTTP_OK, [], ['groups' => 'product:read']);
    }

    #[Route('', name: 'app_product_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $product = $this->productService->create(
                $data['name'] ?? '',
                (float)($data['price'] ?? 0),
                $data['categoryIds'] ?? []
            );
            return $this->json($product, Response::HTTP_CREATED, [], ['groups' => 'product:read']);
        } catch (ProductValidationException|CategoryNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        try {
            $product = $this->productService->findById($id);
            return $this->json($product, Response::HTTP_OK, [], ['groups' => 'product:read']);
        } catch (ProductNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/{id}', name: 'app_product_update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $product = $this->productService->update(
                $id,
                $data['name'] ?? '',
                (float)($data['price'] ?? 0),
                isset($data['categoryIds']) ? $data['categoryIds'] : null
            );
            return $this->json($product, Response::HTTP_OK, [], ['groups' => 'product:read']);
        } catch (ProductNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (ProductValidationException | CategoryNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(string $id): Response
    {
        try {
            $this->productService->delete($id);
            return new Response(null, Response::HTTP_NO_CONTENT);
        } catch (ProductNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
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