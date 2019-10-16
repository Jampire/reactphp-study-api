<?php

require_once __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Factory as LoopFactory;
use React\Http\Server;
use Clue\React\SQLite\Factory as SQLiteFactory;
use React\Socket\Server as Socket;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use App\Router;
use App\Users;
use App\Controller\ListUsersController;
use App\Controller\CreateUserController;
use App\Controller\ViewUserController;
use App\Controller\UpdateUserController;
use App\Controller\DeleteUserController;
use FriendsOfReact\Http\Middleware\Psr15Adapter\PSR15Middleware;
use Middlewares\BasicAuthentication;

$loop = LoopFactory::create();
$dbFactory = new SQLiteFactory($loop);
$db = $dbFactory->openLazy(__DIR__ . '/storage/database.db');
$users = new Users($db);

$routes = new RouteCollector(new Std(), new GroupCountBased());
$routes->get('/users', new ListUsersController($users));
$routes->post('/users', new CreateUserController($users));
$routes->get('/users/{id}', new ViewUserController($users));
$routes->put('/users/{id}', new UpdateUserController($users));
$routes->delete('/users/{id}', new DeleteUserController($users));

$credentials = ['user' => 'secret'];
$basicAuth = new PSR15Middleware($loop, BasicAuthentication::class, [$credentials,]);

$server = new Server([$basicAuth, new Router($routes)]);
$socket = new Socket('127.0.0.1:8001', $loop);
$server->listen($socket);

echo 'Listening on ', str_replace('tcp:', 'http:', $socket->getAddress()), PHP_EOL;
$loop->run();
