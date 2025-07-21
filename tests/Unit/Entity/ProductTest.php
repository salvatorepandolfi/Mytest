<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductCreation(): void
    {
        $product = new Product('Test Product', '10.00', '0.2000');
        
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals('10.00', $product->getPrice());
        $this->assertEquals('0.2000', $product->getVatRate());
        $this->assertNull($product->getId());
    }

    public function testDefaultVatRate(): void
    {
        $product = new Product('Test Product', '10.00');
        
        $this->assertEquals('0.1000', $product->getVatRate());
    }

    public function testCalculateTotalPrice(): void
    {
        $product = new Product('Test Product', '10.00', '0.2000');
        
        $this->assertEquals('50.00', $product->calculateTotalPrice(5));
        $this->assertEquals('10.00', $product->calculateTotalPrice(1));
        $this->assertEquals('0.00', $product->calculateTotalPrice(0));
    }

    public function testCalculateVat(): void
    {
        $product = new Product('Test Product', '10.00', '0.2000'); // 20% VAT
        
        $this->assertEquals('10.00', $product->calculateVat(5)); // 50.00 * 0.20 = 10.00
        $this->assertEquals('2.00', $product->calculateVat(1)); // 10.00 * 0.20 = 2.00
        $this->assertEquals('0.00', $product->calculateVat(0));
    }

    public function testCalculateVatWithDecimalPrecision(): void
    {
        $product = new Product('Test Product', '1.50', '0.1000'); // 10% VAT
        
        // 1.50 * 5 = 7.50, 7.50 * 0.10 = 0.75
        $this->assertEquals('0.75', $product->calculateVat(5));
    }

    public function testSetters(): void
    {
        $product = new Product('Original', '5.00', '0.1000');
        
        $product->setName('Updated Name');
        $product->setPrice('15.99');
        $product->setVatRate('0.2200');
        
        $this->assertEquals('Updated Name', $product->getName());
        $this->assertEquals('15.99', $product->getPrice());
        $this->assertEquals('0.2200', $product->getVatRate());
    }
}