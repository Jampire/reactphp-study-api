<?php

namespace App\Controller;

use App\Auth\JwtAuthenticator;
use App\JsonResponse;
use App\Exceptions\UserNotFoundError;
use Psr\Http\Message\ServerRequestInterface;

final class Login
{
    private $authenticator;

    public function __construct(JwtAuthenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $email = $this->extractEmail($request);
        if (empty($email)) {
            return JsonResponse::badRequest("Field 'email' is required.");
        }

        $this->authenticator->authenticate($email)
            ->then(
                static function(string $token) {
                    return JsonResponse::ok(['token' => $token]);
                },
                static function(UserNotFoundError $error) {
                    return JsonResponse::unauthorized();
                }
             );
    }

    public function extractEmail(ServerRequestInterface $request): ?string
    {
        $params = json_decode((string)$request->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $params['email'] ?? '';
    }
}
