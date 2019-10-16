<?php

namespace App\Exceptions;

use RuntimeException;

class UserNotFoundError extends RuntimeException
{
    public function __construct(string  $message = 'User not found.')
    {
        parent::__construct($message);
    }
}
