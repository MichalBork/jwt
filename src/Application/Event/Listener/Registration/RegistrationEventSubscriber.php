<?php

namespace App\Application\Event\Listener\Registration;

use App\Application\Event\RegistrationCompletedEvent;
use Psr\Log\LoggerInterface;

class RegistrationEventSubscriber implements RegistrationEventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RegistrationCompletedEvent::class => 'onRegistrationCompleted',
        ];
    }

    public function onRegistrationCompleted(RegistrationCompletedEvent $event): void
    {
        $this->logger->info(sprintf('User with email %s has been registered', $event->getEmail()));
    }
}