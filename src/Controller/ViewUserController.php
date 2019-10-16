<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface;
use App\Users;
use App\JsonResponse;
use App\Exceptions\UserNotFoundError;

final class ViewUserController
{
    /** @var Users */
    private $users;

    public function __construct(Users $users)
    {
        $this->users = $users;
    }

    public function __invoke(ServerRequestInterface $request, int $id)
    {
        return $this->users->find($id)
            ->then(
                static function(array $user) {
                    return JsonResponse::ok($user);
                },
                static function(UserNotFoundError $error) {
                    return JsonResponse::notFound($error->getMessage());
                }
            );
    }
}
