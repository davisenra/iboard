<?php

namespace App\Exception;

use Exception;

class BoardException extends Exception
{
    public static function notFound(string $boardRoute): BoardException
    {
        return new BoardException(
            sprintf('No board found for route: %s', $boardRoute)
        );
    }
}
