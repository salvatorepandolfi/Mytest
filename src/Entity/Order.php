<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $totalPrice;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $totalVat;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->totalPrice = '0.00';
        $this->totalVat = '0.00';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): self
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getTotalVat(): string
    {
        return $this->totalVat;
    }

    public function setTotalVat(string $totalVat): self
    {
        $this->totalVat = $totalVat;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }

    public function calculateTotals(): void
    {
        $totalPrice = '0.00';
        $totalVat = '0.00';

        foreach ($this->items as $item) {
            $totalPrice = bcadd($totalPrice, $item->getPrice(), 2);
            $totalVat = bcadd($totalVat, $item->getVat(), 2);
        }

        $this->totalPrice = $totalPrice;
        $this->totalVat = $totalVat;
    }
}