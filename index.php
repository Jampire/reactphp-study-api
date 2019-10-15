<?php

require_once __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Factory as LoopFactory;
use React\Http\Response;
use React\Http\Server;
use Clue\React\SQLite\Factory as SQLiteFactory;
use React\Socket\Server as Socket;
use Clue\React\SQLite\Result as SQLiteResult;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use App\Router;
use Psr\Http\Message\ServerRequestInterface;

$loop = LoopFactory::create();

$dbFactory = new SQLiteFactory($loop);
$db = $dbFactory->openLazy(__DIR__ . '/storage/database.db');

$listUsers = static function() use ($db) {
    return $db->query('SELECT id, name, email FROM users ORDER BY id')
        ->then(static function(SQLiteResult $result) {
            $users = json_encode($result->rows);

            return new Response(200, [
                'Content-type' => 'application/json',
                'X-Powered-By' => 'Yo-ho-ho',
            ], $users);
        });
};

$createUser = static function(ServerRequestInterface $request) use ($db) {
    $user = json_decode((string)$request->getBody(), true, 512, JSON_THROW_ON_ERROR);

    //var_dump($user);

    return $db->query('INSERT INTO users(name, email) VALUES (?, ?)', $user)
        ->then(
            static function() {
                return new Response(201);
            },
            static function(Exception $error) {
                return new Response(400, [
                    'Content-Type' => 'application/json',
                ], json_encode([
                    'error' => $error->getMessage(),
                ], JSON_THROW_ON_ERROR, 512));
            }
        );
};

$routes = new RouteCollector(new Std(), new GroupCountBased());
$routes->get('/users', $listUsers);
$routes->post('/users', $createUser);

$server = new Server(new Router($routes));
$socket = new Socket('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ', str_replace('tcp:', 'http:', $socket->getAddress()), PHP_EOL;
$loop->run();
