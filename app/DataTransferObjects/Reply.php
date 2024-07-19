<?php

namespace App\DataTransferObjects;

use DateTimeInterface;

readonly class Reply
{
    public function __construct(
        public int $replyId,
        public int $threadId,
        public string $content,
        public ?string $options,
        public ?File $file,
        public DateTimeInterface $publishedAt,
    ) {}
}
