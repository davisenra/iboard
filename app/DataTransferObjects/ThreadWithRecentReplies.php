<?php

namespace App\DataTransferObjects;

use DateTimeInterface;

readonly class ThreadWithRecentReplies
{
    public function __construct(
        public int $threadId,
        public string $subject,
        public string $content,
        public string $file,
        public DateTimeInterface $publishedAt,
        public DateTimeInterface $lastRepliedAt,
        /** @var Reply[] $recentReplies */
        public array $recentReplies,
    ) {}
}
