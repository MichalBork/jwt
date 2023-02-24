<?php

namespace App\Application\Infrastructure\Persistence\Doctrine\Repository;

use App\Application\Domain\User\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findOneByEmail(string $getEmail): ?User;
}
