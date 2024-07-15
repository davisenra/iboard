<?php

namespace App\Actions\NewThread;

use SplFileInfo;

readonly class NewThreadPayload
{
    public function __construct(
        public int $boardId,
        public ?string $subject,
        public string $content,
        public SplFileInfo $file,
    ) {}
}
