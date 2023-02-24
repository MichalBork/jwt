<?php

namespace App\Application\Ui\Controller;

use App\Application\Command\LoginCommand;
use App\Application\Command\LogoutCommand;
use App\Application\Command\RefreshTokenCommand;
use App\Application\Command\RegistrationCommand;
use App\Application\Event\Listener\Registration\RegistrationEventSubscriberInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegistrationController extends AbstractController
{
    use HandleTrait;

    private MessageBusInterface $commandBus;

    /**
     * @param MessageBusInterface $commandBus
     */
    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
        $this->messageBus = $commandBus;
    }


    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        EventDispatcherInterface $eventDispatcher,
    ): Response {
        $data = json_decode($request->getContent(), true);


        try {
            $this->commandBus->dispatch(new RegistrationCommand($data['email'], $data['password']));
        } catch (HandlerFailedException $e) {
            return $this->handleError($e);
        }

        return new JsonResponse([
            'message' => 'User registered successfully',
        ], Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        try {
            $token = $this->handle(new LoginCommand($data['email'], $data['password']));
        } catch (\Exception $e) {
            return $this->handleError($e);
        }

        return new JsonResponse([
            'message' => 'User logged in successfully',
            'token' => $token,
        ], Response::HTTP_OK);
    }


    #[Route('/refresh', name: 'refresh', methods: ['POST'])]
    public function refresh(Request $request): Response
    {
        $token = json_decode($request->getContent(), true)['token'];
        try {
            $refreshToken = $this->handle(new RefreshTokenCommand($token));
        } catch (HandlerFailedException $e) {
            $this->handleError($e);
        }

        return new JsonResponse([
            'message' => 'Token refreshed successfully',
            'refresh_token' => $refreshToken,
        ], Response::HTTP_OK);
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(Request $request): Response
    {
        $token = json_decode($request->getContent(), true)['token'];

        try {
            $this->handle(new LogoutCommand($token));
        } catch (HandlerFailedException $e) {
            $this->handleError($e);
        }

        return new JsonResponse([
            'message' => 'User logged out successfully',
        ], Response::HTTP_OK);
    }


    private function handleError(\Exception $e): Response
    {
        $previousException = $e->getPrevious();
        if ($previousException instanceof \Exception) {
            return new JsonResponse([
                'message' => $previousException->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'message' => $e->getMessage(),
        ], Response::HTTP_BAD_REQUEST);
    }
}
