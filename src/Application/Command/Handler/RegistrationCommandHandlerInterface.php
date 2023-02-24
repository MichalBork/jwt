<?php

namespace App\Application\Command\Handler;

use App\Application\Command\RegistrationCommand;

interface RegistrationCommandHandlerInterface
{
    public function handle(RegistrationCommand $command): void;
}