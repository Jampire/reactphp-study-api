<?php

namespace App\Auth;

use App\Users;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

final class JwtAuthenticator
{
    private const HEADER_VALUE_PATTERN = "/Bearer\s+(.*)$/i";

    private $encoder;

    private $users;

    public function __construct(JWTEncoder $encoder, Users $users)
    {
        $this->encoder = $encoder;
        $this->users = $users;
    }

    public function validate(ServerRequestInterface $request): bool
    {
        $jwt = $this->extractToken($request);
        if (empty($jwt)) {
            return false;
        }

        $payload = $this->encoder->decode($jwt);

        return !empty($payload);
    }

    public function authenticate(string $email): PromiseInterface
    {
        return $this->users->findByEmail($email)
            ->then(
                function (array $user) {
                    return $this->encoder->encode(['id' => $user['id']]);
                },
                static function (\Exception $exception) {
                    throw $exception;
                }
            );
    }

    private function extractToken(ServerRequestInterface $request): ?string
    {
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            return null;
        }

        if (preg_match(self::HEADER_VALUE_PATTERN, $authHeader[0], $matches)) {
            return $matches[1];
        }

        return null;
    }
}
