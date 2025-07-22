<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $price;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 4)]
    private string $vatRate;

    public function __construct(string $name, string $price, string $vatRate = '0.1000')
    {
        $this->name = $name;
        $this->price = $price;
        $this->vatRate = $vatRate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getVatRate(): string
    {
        return $this->vatRate;
    }

    public function setVatRate(string $vatRate): self
    {
        $this->vatRate = $vatRate;
        return $this;
    }

    public function calculateVat(int $quantity): string
    {
        $totalPrice = bcmul($this->price, (string) $quantity, 2);
        return bcmul($totalPrice, $this->vatRate, 2);
    }

    public function calculateTotalPrice(int $quantity): string
    {
        return bcmul($this->price, (string) $quantity, 2);
    }
}