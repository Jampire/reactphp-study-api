<?php

namespace App;

use App\Exceptions\UserNotFoundError;
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
        return $this->db->query('INSERT INTO users(name, email) VALUES (?, ?)', [$name, $email])
            ->then(
                static function(SQLiteResult $result) {
                    return $result->insertId;
                }
            );
    }

    public function find(int $id): PromiseInterface
    {
        return $this->db->query('SELECT * FROM users WHERE id = ?', [$id])
            ->then(
                static function(SQLiteResult $result) {
                    if (empty($result->rows)) {
                        throw new UserNotFoundError();
                    }

                    return $result->rows[0];
                }
            );
    }

    public function update(int $id, string $newName): PromiseInterface
    {
        return $this->find($id)
            ->then(
                function() use ($id, $newName) {
                    $this->db->query('UPDATE users SET name = ? WHERE id = ?', [$newName, $id]);
                }
            );
    }

    public function delete(int $id): PromiseInterface
    {
        return $this->db->query('DELETE FROM users WHERE id = ?', [$id])
            ->then(
                static function(SQLiteResult $result) {
                    if ($result->changed === 0) {
                        throw new UserNotFoundError();
                    }
                }
            );
    }
}
