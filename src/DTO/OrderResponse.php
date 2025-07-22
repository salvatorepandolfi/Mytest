<?php

namespace App\DTO;

class OrderResponse
{
    public int $order_id;
    public string $order_price;
    public string $order_vat;

    /**
     * @var OrderItemResponse[]
     */
    public array $items;

    /**
     * @param OrderItemResponse[] $items
     */
    public function __construct(int $order_id, string $order_price, string $order_vat, array $items)
    {
        $this->order_id = $order_id;
        $this->order_price = $order_price;
        $this->order_vat = $order_vat;
        $this->items = $items;
    }
}

class OrderItemResponse
{
    public int $product_id;
    public int $quantity;
    public string $price;
    public string $vat;

    public function __construct(int $product_id, int $quantity, string $price, string $vat)
    {
        $this->product_id = $product_id;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->vat = $vat;
    }
}