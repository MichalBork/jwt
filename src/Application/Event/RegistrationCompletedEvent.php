<?php

namespace App\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RegistrationCompletedEvent extends Event
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

}