<?php

namespace App\Actions\ShowThread;

readonly class ShowThreadPayload
{
    public function __construct(
        public int $threadId,
    ) {}
}
