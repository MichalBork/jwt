<?php

namespace App\Application\Ui\Controller;

use App\Application\Domain\User\Product;
use App\Application\Infrastructure\Persistence\Doctrine\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product', name: 'product_')]
class ProductController extends AbstractController
{


    public function __construct(private ProductRepository $productRepository)
    {
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $product = $this->productRepository->findOneById($request->request->get('id'));

        return $this->json($product);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        $product = $this->productRepository->findOneById($request->request->get('id'));
        $this->productRepository->deleteProduct($product);

        return $this->json(['message' => 'Product deleted successfully']);
    }

    #[Route('/', name: 'find_all', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $products = $this->productRepository->findAllProducts();

        return $this->json($products);
    }


    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $product = new Product($data['name'], $data['price'], $data['description'], $data['image']);
        $this->productRepository->createProduct($product);

        return $this->json(['message' => 'Product created successfully']);
    }
}