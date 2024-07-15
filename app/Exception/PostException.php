<?php

namespace App\Exception;

use Exception;

class PostException extends Exception
{
    public static function threadNotFound(int $threadId): PostException
    {
        return new PostException(
            sprintf('No thread found for id: %d', $threadId)
        );
    }
}
