<?php

namespace App\Actions\ReplyToThread;

use SplFileInfo;

readonly class ReplyPayload
{
    public function __construct(
        public int $threadId,
        public string $content,
        public ?SplFileInfo $file = null,
    ) {}
}
