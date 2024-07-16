<?php

namespace App\DataTransferObjects;

readonly class NewlyCreatedThread
{
    public function __construct(
        public int $threadId
    ) {}
}
