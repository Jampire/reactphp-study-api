<?php

namespace App;

use Clue\React\SQLite\DatabaseInterface;
use Clue\React\SQLite\Result as SQLiteResult;
use React\Promise\PromiseInterface;

final class Users
{
    /** @var DatabaseInterface */
    private $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function all(): PromiseInterface
    {
        return $this->db->query('SELECT id, name, email FROM users ORDER BY id')
                        ->then(
                            static function (SQLiteResult $result) {
                                return $result->rows;
                            });
    }

    public function create(string $name, string $email): PromiseInterface
    {
        return $this->db->query('INSERT INTO users(name, email) VALUES (?, ?)', [$name, $email]);
    }
}
