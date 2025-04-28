<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Service\CategoryService;
use App\Domain\Exception\Category\CategoryCodeNotUniqueException;
use App\Domain\Exception\Category\CategoryNotFoundException;
use App\Domain\Exception\Category\CategoryValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/categories')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }

    #[Route('', name: 'app_category_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->findAll();
        return $this->json($categories, Response::HTTP_OK, [], ['groups' => 'category:read']);
    }

    #[Route('', name: 'app_category_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $category = $this->categoryService->create($data['code'] ?? '');
            return $this->json($category, Response::HTTP_CREATED, [], ['groups' => 'category:read']);
        } catch (CategoryValidationException | CategoryCodeNotUniqueException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        try {
            $category = $this->categoryService->findById($id);
            return $this->json($category, Response::HTTP_OK, [], ['groups' => 'category:read']);
        } catch (CategoryNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/{id}', name: 'app_category_update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $category = $this->categoryService->update($id, $data['code'] ?? '');
            return $this->json($category, Response::HTTP_OK, [], ['groups' => 'category:read']);
        } catch (CategoryNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (CategoryValidationException | CategoryCodeNotUniqueException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['DELETE'])]
    public function delete(string $id): Response
    {
        try {
            $this->categoryService->delete($id);
            return new Response(null, Response::HTTP_NO_CONTENT);
        } catch (CategoryNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}