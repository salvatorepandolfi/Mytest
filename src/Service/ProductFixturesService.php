<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;

class ProductFixturesService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    public function loadSampleProducts(): void
    {
        // Check if products already exist
        $existingProducts = $this->productRepository->findAll();
        if (!empty($existingProducts)) {
            return; // Products already loaded
        }

        $products = [
            new Product('Sample Product 1', '2.00', '0.1000'), // 10% VAT
            new Product('Sample Product 2', '1.50', '0.1000'), // 10% VAT
            new Product('Sample Product 3', '3.00', '0.1000'), // 10% VAT
            new Product('Premium Product', '15.99', '0.2200'), // 22% VAT
            new Product('Basic Item', '0.99', '0.1000'), // 10% VAT
        ];

        foreach ($products as $product) {
            $this->productRepository->save($product);
        }

        $this->productRepository->getEntityManager()->flush();
    }
}