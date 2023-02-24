<?php

namespace App\Application\Ui\Controller;

use App\Application\Command\LoginCommand;
use App\Application\Command\RegistrationCommand;
use App\Application\Event\Listener\Registration\RegistrationEventSubscriberInterface;
use App\Application\Event\RegistrationCompletedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBus;
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
        RegistrationEventSubscriberInterface $registrationEventSubscriber,
    ): Response {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        try {

        $this->commandBus->dispatch(new RegistrationCommand($email, $password));
    } catch (HandlerFailedException $e) {
            dd($e->getPrevious());
$previousException = $e->getPrevious();
}

        return new JsonResponse([
                                    'message' => 'User registered successfully',
                                ], Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        try {
            $token = $this->handle(new LoginCommand($email, $password));
        } catch (HandlerFailedException $e) {
            $previousException = $e->getPrevious();
        }

        return new JsonResponse([
                                    'message' => 'User logged in successfully',
                                    'token' => $token,
                                ], Response::HTTP_OK);
    }

}