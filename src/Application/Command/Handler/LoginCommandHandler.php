<?php

namespace App\Application\Command\Handler;

use App\Application\Command\LoginCommand;
use App\Application\Domain\User\User;
use App\Application\Infrastructure\Persistence\Doctrine\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

#[AsMessageHandler]
class LoginCommandHandler
{
    private UserRepositoryInterface $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private JWTTokenManagerInterface $JWTTokenManager;
    private UserProviderInterface $userProvider;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        JWTTokenManagerInterface $JWTTokenManager,
        UserProviderInterface $userProvider
    ) {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->JWTTokenManager = $JWTTokenManager;
        $this->userProvider = $userProvider;
    }


    public function __invoke(LoginCommand $command): string
    {
        $user = $this->userRepository->findOneByEmail($command->getEmail());


        if (!$user) {
            throw new \Exception('User not found');
        }


        if (!$this->userPasswordHasher->isPasswordValid($user, $command->getPassword())) {
            throw new \Exception('Invalid password');
        }
        $user->createSessionToken();
        $this->userRepository->save($user);
        $user = $this->userProvider->loadUserByIdentifier($user->getEmail());

        return $this->JWTTokenManager->create($user);
    }
}