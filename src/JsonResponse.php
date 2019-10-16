<?php

namespace App;

use React\Http\Response;

final class JsonResponse extends Response
{
    public function __construct(int $statusCode, $data = null)
    {
        $body = $data ? json_encode($data, JSON_THROW_ON_ERROR, 512) : null;

        parent::__construct($statusCode, [
            'Content-type' => 'application/json',
            'X-Powered-By' => 'Yo-ho-ho',
        ], $body);
    }

    public static function ok($data = null): self
    {
        return new self(200, $data);
    }

    public static function created($data = null): self
    {
        return new self(201, $data);
    }

    public static function badRequest(string $error): self
    {
        return new self(400, ['error' => $error]);
    }

    public static function notFound(string $error): self
    {
        return new self(404, ['error' => $error]);
    }

    public static function noContent(): self
    {
        return new self(204);
    }
}
