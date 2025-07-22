<?php

namespace App\Service;

use App\DTO\OrderItemData;
use App\DTO\OrderItemResponse;
use App\DTO\OrderRequest;
use App\DTO\OrderResponse;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderService
{
    public function __construct(
        private ProductRepository $productRepository,
        private OrderRepository $orderRepository
    ) {
    }

    public function createOrder(OrderRequest $orderRequest): OrderResponse
    {
        // Validate that all products exist
        $productIds = array_map(fn(OrderItemData $item) => $item->product_id, $orderRequest->order->items);
        $products = $this->productRepository->findByIds($productIds);
        
        if (count($products) !== count($productIds)) {
            $foundIds = array_map(fn(Product $product) => $product->getId(), $products);
            $missingIds = array_diff($productIds, $foundIds);
            throw new NotFoundHttpException(sprintf('Products not found: %s', implode(', ', $missingIds)));
        }

        // Create product lookup for easier access
        $productLookup = [];
        foreach ($products as $product) {
            $productLookup[$product->getId()] = $product;
        }

        // Create order
        $order = new Order();
        $itemResponses = [];

        // Process each order item
        foreach ($orderRequest->order->items as $itemData) {
            $product = $productLookup[$itemData->product_id];
            
            // Calculate price and VAT for this item
            $itemPrice = $product->calculateTotalPrice($itemData->quantity);
            $itemVat = $product->calculateVat($itemData->quantity);
            
            // Create order item entity
            $orderItem = new OrderItem(
                $itemData->product_id,
                $itemData->quantity,
                $itemPrice,
                $itemVat
            );
            
            $order->addItem($orderItem);
            
            // Create response item
            $itemResponses[] = new OrderItemResponse(
                $itemData->product_id,
                $itemData->quantity,
                $itemPrice,
                $itemVat
            );
        }

        // Calculate order totals
        $order->calculateTotals();

        // Save the order
        $this->orderRepository->save($order, true);

        // Return response
        return new OrderResponse(
            $order->getId(),
            $order->getTotalPrice(),
            $order->getTotalVat(),
            $itemResponses
        );
    }
}