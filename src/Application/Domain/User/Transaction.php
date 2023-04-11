<?php

namespace App\Application\Domain\User;
use App\Application\Infrastructure\Persistence\Doctrine\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class), ORM\HasLifecycleCallbacks, ORM\Table(name: 'transactions')]
class Transaction
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private $amount;


    private Product $product;

}