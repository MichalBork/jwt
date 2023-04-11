<?php

namespace App\Application\Infrastructure\Persistence\Doctrine\Repository;

use App\Application\Domain\User\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Product::class);
    }


    public function findOneById(string $getId): ?Product
    {
        return $this->findOneBy(['id' => $getId]);
    }

    public function findOneByName(string $getName): ?Product
    {
        return $this->findOneBy(['name' => $getName]);
    }

    public function createProduct(Product $product): void
    {
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
    }

    public function updateProduct(Product $product): void
    {
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
    }

    public function deleteProduct(Product $product): void
    {
        $this->getEntityManager()->remove($product);
        $this->getEntityManager()->flush();
    }

    public function findAllProducts(): array
    {
        return $this->findAll();
    }

}