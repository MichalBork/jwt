<?php

namespace App\Application\Event\Listener\Registration;

use App\Application\Event\RegistrationCompletedEvent;

interface RegistrationEventSubscriberInterface
{
    public function onRegistrationCompleted(RegistrationCompletedEvent $event): void;
}