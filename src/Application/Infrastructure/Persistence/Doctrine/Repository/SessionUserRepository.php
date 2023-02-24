<?php

namespace App\Application\Infrastructure\Persistence\Doctrine\Repository;

use App\Application\Domain\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SessionUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, User::class);
    }


    public function findOneByEmail(string $getEmail): ?User
    {

        return $this->findOneBy(['email' => $getEmail]);
    }
}
