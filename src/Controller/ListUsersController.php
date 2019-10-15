<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface;
use App\Users;
use App\JsonResponse;

final class ListUsersController
{
    /** @var Users */
    private $users;

    public function __construct(Users $users)
    {
        $this->users = $users;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->users->all()
            ->then(static function(Array $users) {
                return JsonResponse::ok($users);
            });
    }
}
