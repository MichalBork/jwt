<?php

namespace App\Application\Command;

class RefreshTokenCommand
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }



    public function getToken(): string
    {
        return $this->token;
    }
}
