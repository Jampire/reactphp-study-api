<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface;
use App\Users;
use App\JsonResponse;
use App\Exceptions\UserNotFoundError;

final class UpdateUserController
{
    /** @var Users */
    private $users;

    public function __construct(Users $users)
    {
        $this->users = $users;
    }

    public function __invoke(ServerRequestInterface $request, int $id)
    {
        $name = $this->extractName($request);
        if (empty($name)) {
            return JsonResponse::badRequest('"name" field is required.');
        }

        return $this->users->update($id, $name)
                           ->then(
                               static function() {
                                   return JsonResponse::noContent();
                               },
                               static function(UserNotFoundError $error) {
                                   return JsonResponse::notFound($error->getMessage());
                               }
                           );
    }

    private function extractField(ServerRequestInterface $request, string $field): ?string
    {
        $params = json_decode((string)$request->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $params[$field] ?? null;
    }

    private function extractName(ServerRequestInterface $request): ?string
    {
        return $this->extractField($request, 'name');
    }
}
