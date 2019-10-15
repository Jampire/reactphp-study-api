<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface;
use App\Users;
use App\JsonResponse;

final class CreateUserController
{
    /** @var Users */
    private $users;

    public function __construct(Users $users)
    {
        $this->users = $users;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $user = json_decode((string)$request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $name = $user['name'] ?? '';
        $email = $user['email'] ?? '';

        return $this->users->create($name, $email)
            ->then(
                static function(int $id) {
                    return JsonResponse::created(['user_id' => $id]);
                },
                static function(\Exception $error) {
                    return JsonResponse::badRequest($error->getMessage());
                }
            );
    }
}
