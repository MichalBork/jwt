<?php

namespace App\Application\Command\Handler;

use App\Application\Command\LoginCommand;
use App\Application\Domain\User\User;
use App\Application\Exception\UserNotFoundException;
use App\Application\Infrastructure\Persistence\Doctrine\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use App\Application\Exception\InvalidPasswordException;

#[AsMessageHandler]
class LoginCommandHandler
{
    private UserRepositoryInterface $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private JWTEncoderInterface $JWTTokenManager;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        JWTEncoderInterface $JWTTokenManager,
    ) {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->JWTTokenManager = $JWTTokenManager;
    }


    /**
     * @throws JWTEncodeFailureException
     * @throws InvalidPasswordException
     * @throws UserNotFoundException
     */
    public function __invoke(LoginCommand $command): string
    {
        $user = $this->userRepository->findOneByEmail($command->getEmail());


        if (!$user) {
            throw new UserNotFoundException('User not found');
        }


        if (!$this->userPasswordHasher->isPasswordValid($user, $command->getPassword())) {
            throw new InvalidPasswordException('Password invalid');
        }
        $user->createSessionToken();
        $this->userRepository->save($user);
        return $this->JWTTokenManager->encode($user->getArrayForEncode());
    }
}
