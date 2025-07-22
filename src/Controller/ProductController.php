<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\ProductFixturesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductFixturesService $fixturesService
    ) {
    }

    #[Route('/api/products', name: 'list_products', methods: ['GET'])]
    public function listProducts(): JsonResponse
    {
        // Load sample products if none exist
        $this->fixturesService->loadSampleProducts();
        
        $products = $this->productRepository->findAll();
        
        $productsData = array_map(function($product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'vat_rate' => $product->getVatRate()
            ];
        }, $products);
        
        return new JsonResponse($productsData);
    }

    #[Route('/api/products/fixtures', name: 'load_fixtures', methods: ['POST'])]
    public function loadFixtures(): JsonResponse
    {
        $this->fixturesService->loadSampleProducts();
        
        return new JsonResponse([
            'message' => 'Sample products loaded successfully'
        ]);
    }
}