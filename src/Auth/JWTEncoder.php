<?php

namespace App\Auth;

use Firebase\JWT\JWT;

final class JWTEncoder
{
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function decode(string $jwt): array
    {
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);

        return (array)$decoded;
    }

    public function encode(array $payload): string
    {
        return JWT::encode($payload, $this->key);
    }
}
