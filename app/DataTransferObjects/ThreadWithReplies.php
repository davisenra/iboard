<?php

namespace App\DataTransferObjects;

use DateTimeInterface;

readonly class ThreadWithReplies
{
    public function __construct(
        public string $boardRoute,
        public string $boardName,
        public int $threadId,
        public ?string $subject,
        public string $content,
        public File $file,
        public DateTimeInterface $publishedAt,
        public DateTimeInterface $lastRepliedAt,
        /** @var Reply[] */
        public array $replies,
    ) {}
}
