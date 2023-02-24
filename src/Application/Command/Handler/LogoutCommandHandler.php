<?php

namespace App\Application\Command\Handler;

use App\Application\Command\LogoutCommand;
use App\Application\Exception\InvalidTokenException;
use App\Application\Infrastructure\Persistence\Doctrine\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LogoutCommandHandler
{
    private JWTEncoderInterface $JWTTokenManager;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        JWTEncoderInterface $JWTTokenManager,
    ) {
        $this->userRepository = $userRepository;
        $this->JWTTokenManager = $JWTTokenManager;
    }

    /**
     * @throws JWTDecodeFailureException
     * @throws InvalidTokenException
     */
    public function __invoke(LogoutCommand $command): void
    {
        $user = $this->JWTTokenManager->decode($command->getToken());
        $userData = $this->userRepository->findOneBy(['email' => $user['email']]);
        if ($userData->getArrayForEncode()['sessionToken'] != $user['sessionToken']) {
            throw new InvalidTokenException('Invalid token');
        }
        $userData->createSessionToken();
        $this->userRepository->save($userData);
    }
}
