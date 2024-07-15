<?php

namespace App\Actions\ReplyToThread;

use SplFileInfo;

readonly class ReplyPayload
{
    public function __construct(
        public int $threadId,
        public string $content,
        public ?string $options = null,
        public ?SplFileInfo $file = null,
    ) {}

    public function isSage(): bool
    {
        return $this->options === 'sage';
    }
}
