<?php

namespace App\Tests\Integration;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->productRepository = $this->entityManager->getRepository(Product::class);
        
        // Clear database and add test products
        $this->clearDatabase();
        $this->loadTestProducts();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }

    public function testCreateOrderSuccess(): void
    {
        $client = static::createClient();
        
        $requestData = [
            'order' => [
                'items' => [
                    ['product_id' => 1, 'quantity' => 1],
                    ['product_id' => 2, 'quantity' => 5],
                    ['product_id' => 3, 'quantity' => 1]
                ]
            ]
        ];

        $client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('order_id', $responseData);
        $this->assertArrayHasKey('order_price', $responseData);
        $this->assertArrayHasKey('order_vat', $responseData);
        $this->assertArrayHasKey('items', $responseData);
        
        $this->assertIsInt($responseData['order_id']);
        $this->assertEquals('12.50', $responseData['order_price']);
        $this->assertEquals('1.25', $responseData['order_vat']);
        $this->assertCount(3, $responseData['items']);
        
        // Check first item
        $firstItem = $responseData['items'][0];
        $this->assertEquals(1, $firstItem['product_id']);
        $this->assertEquals(1, $firstItem['quantity']);
        $this->assertEquals('2.00', $firstItem['price']);
        $this->assertEquals('0.20', $firstItem['vat']);
    }

    public function testCreateOrderWithInvalidProduct(): void
    {
        $client = static::createClient();
        
        $requestData = [
            'order' => [
                'items' => [
                    ['product_id' => 999, 'quantity' => 1] // Non-existent product
                ]
            ]
        ];

        $client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Product not found', $responseData['error']);
    }

    public function testCreateOrderWithInvalidQuantity(): void
    {
        $client = static::createClient();
        
        $requestData = [
            'order' => [
                'items' => [
                    ['product_id' => 1, 'quantity' => -1] // Invalid quantity
                ]
            ]
        ];

        $client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Validation failed', $responseData['error']);
    }

    public function testCreateOrderWithEmptyItems(): void
    {
        $client = static::createClient();
        
        $requestData = [
            'order' => [
                'items' => []
            ]
        ];

        $client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testCreateOrderWithInvalidJson(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"invalid": json}'
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Invalid JSON format', $responseData['error']);
    }

    public function testHealthCheck(): void
    {
        $client = static::createClient();

        $client->request('GET', '/health');

        $this->assertResponseIsSuccessful();
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('healthy', $responseData['status']);
        $this->assertArrayHasKey('timestamp', $responseData);
    }

    private function clearDatabase(): void
    {
        // Clear all tables
        $this->entityManager->createQuery('DELETE FROM App\Entity\OrderItem')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Order')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Product')->execute();
    }

    private function loadTestProducts(): void
    {
        $products = [
            new Product('Sample Product 1', '2.00', '0.1000'),
            new Product('Sample Product 2', '1.50', '0.1000'),
            new Product('Sample Product 3', '3.00', '0.1000')
        ];

        foreach ($products as $product) {
            $this->entityManager->persist($product);
        }
        
        $this->entityManager->flush();
    }
}