<?php

namespace App\Actions\ShowBoard;

readonly class ShowBoardPayload
{
    public function __construct(
        public int $page,
        public string $boardRoute
    ) {}
}
