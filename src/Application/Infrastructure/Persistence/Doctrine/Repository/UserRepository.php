<?php

namespace App\Application\Infrastructure\Persistence\Doctrine\Repository;

use App\Application\Domain\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;


class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface,ServiceEntityRepositoryInterface, UserLoaderInterface
{

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, User::class);

    }

    public function save(User $user): void
    {
        $entity = $this->getEntityManager();
        $entity->persist($user);
        $entity->flush();
    }

    public function findOneByEmail(string $getEmail): ?User
    {

        return $this->findOneBy(['email' => $getEmail]);
    }

    public function loadUserByIdentifier(string $usernameOrEmail): ?User
    {
       return $this->findOneByEmail($usernameOrEmail);

    }
}