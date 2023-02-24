<?php

namespace App\Application\Infrastructure\Persistence\JWT;

interface JWTEncodableInterface
{
    public function getArrayForEncode(): array;
}
