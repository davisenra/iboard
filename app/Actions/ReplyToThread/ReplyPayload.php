<?php

namespace App\Actions\ReplyToThread;

use Illuminate\Http\UploadedFile;

readonly class ReplyPayload
{
    public function __construct(
        public int $threadId,
        public string $content,
        public string $ipAddress,
        public ?string $options = null,
        public ?UploadedFile $file = null,
    ) {}

    public function isSage(): bool
    {
        return $this->options === 'sage';
    }
}
