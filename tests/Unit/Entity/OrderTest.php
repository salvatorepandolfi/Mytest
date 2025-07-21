<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Order;
use App\Entity\OrderItem;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testOrderCreation(): void
    {
        $order = new Order();
        
        $this->assertNull($order->getId());
        $this->assertEquals('0.00', $order->getTotalPrice());
        $this->assertEquals('0.00', $order->getTotalVat());
        $this->assertInstanceOf(\DateTimeImmutable::class, $order->getCreatedAt());
        $this->assertCount(0, $order->getItems());
    }

    public function testAddItem(): void
    {
        $order = new Order();
        $item = new OrderItem(1, 2, '10.00', '1.00');
        
        $order->addItem($item);
        
        $this->assertCount(1, $order->getItems());
        $this->assertTrue($order->getItems()->contains($item));
        $this->assertSame($order, $item->getOrder());
    }

    public function testAddSameItemTwice(): void
    {
        $order = new Order();
        $item = new OrderItem(1, 2, '10.00', '1.00');
        
        $order->addItem($item);
        $order->addItem($item); // Adding same item again
        
        $this->assertCount(1, $order->getItems()); // Should still be 1
    }

    public function testRemoveItem(): void
    {
        $order = new Order();
        $item = new OrderItem(1, 2, '10.00', '1.00');
        
        $order->addItem($item);
        $this->assertCount(1, $order->getItems());
        
        $order->removeItem($item);
        $this->assertCount(0, $order->getItems());
        $this->assertNull($item->getOrder());
    }

    public function testCalculateTotals(): void
    {
        $order = new Order();
        
        $item1 = new OrderItem(1, 2, '10.00', '1.00');
        $item2 = new OrderItem(2, 1, '5.50', '0.55');
        
        $order->addItem($item1);
        $order->addItem($item2);
        
        $order->calculateTotals();
        
        $this->assertEquals('15.50', $order->getTotalPrice()); // 10.00 + 5.50
        $this->assertEquals('1.55', $order->getTotalVat()); // 1.00 + 0.55
    }

    public function testCalculateTotalsWithEmptyOrder(): void
    {
        $order = new Order();
        
        $order->calculateTotals();
        
        $this->assertEquals('0.00', $order->getTotalPrice());
        $this->assertEquals('0.00', $order->getTotalVat());
    }

    public function testSetters(): void
    {
        $order = new Order();
        
        $order->setTotalPrice('99.99');
        $order->setTotalVat('9.99');
        
        $this->assertEquals('99.99', $order->getTotalPrice());
        $this->assertEquals('9.99', $order->getTotalVat());
    }
}