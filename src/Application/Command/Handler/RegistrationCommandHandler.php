<?php

namespace App\Application\Command\Handler;

use App\Application\Command\RegistrationCommand;
use App\Application\Domain\User\User;
use App\Application\Infrastructure\Persistence\Doctrine\Repository\UserRepositoryInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
class RegistrationCommandHandler
{

    private UserRepositoryInterface $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserPasswordHasherInterface $userPasswordHasher
    )
    {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }


    public function __invoke(RegistrationCommand $command)
    {
        $user = new User($command->getEmail(), $command->getPassword());
        $this->userPasswordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
        $this->userRepository->save($user);
    }


}