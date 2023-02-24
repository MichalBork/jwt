<?php

namespace App\Application\Command\Handler;

use App\Application\Command\RefreshTokenCommand;
use App\Application\Exception\InvalidTokenException;
use App\Application\Exception\TokenExpiredException;
use App\Application\Infrastructure\Persistence\Doctrine\Repository\SessionUserRepository;
use App\Application\Infrastructure\Persistence\Doctrine\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
class RefreshTokenCommandHandler
{
    private UserRepositoryInterface $userRepository;
    private JWTEncoderInterface $JWTTokenManager;

    public function __construct(
        UserRepositoryInterface $userRepository,
        JWTEncoderInterface $JWTTokenManager,
    ) {
        $this->userRepository = $userRepository;
        $this->JWTTokenManager = $JWTTokenManager;
    }

    /**
     * @throws JWTEncodeFailureException
     * @throws InvalidTokenException
     * @throws TokenExpiredException
     */
    public function __invoke(RefreshTokenCommand $command): string
    {
        $user = $this->JWTTokenManager->decode($command->getToken());
        if ($user['exp'] < time()) {
            throw new TokenExpiredException('Token expired');
        }

        $userData = $this->userRepository->findOneByEmail($user['email']);

        if ($userData->getArrayForEncode()['sessionToken']->toString() != $user['sessionToken']) {
            throw new InvalidTokenException('Invalid token');
        }

        return $this->JWTTokenManager->encode($user);
    }
}
