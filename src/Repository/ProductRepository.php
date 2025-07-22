<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param int[] $productIds
     * @return Product[]
     */
    public function findByIds(array $productIds): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $productIds)
            ->getQuery()
            ->getResult();
    }

    public function save(Product $product, bool $flush = false): void
    {
        $this->getEntityManager()->persist($product);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $product, bool $flush = false): void
    {
        $this->getEntityManager()->remove($product);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}