<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class OrderRequest
{
    #[Assert\NotNull]
    #[Assert\Valid]
    public OrderData $order;

    public function __construct()
    {
        $this->order = new OrderData();
    }
}

class OrderData
{
    /**
     * @var OrderItemData[]
     */
    #[Assert\NotBlank]
    #[Assert\Count(min: 1)]
    #[Assert\Valid]
    public array $items = [];
}

class OrderItemData
{
    #[Assert\NotNull]
    #[Assert\Type('integer')]
    #[Assert\GreaterThan(0)]
    public int $product_id;

    #[Assert\NotNull]
    #[Assert\Type('integer')]
    #[Assert\GreaterThan(0)]
    public int $quantity;

    public function __construct(int $product_id = 0, int $quantity = 0)
    {
        $this->product_id = $product_id;
        $this->quantity = $quantity;
    }
}