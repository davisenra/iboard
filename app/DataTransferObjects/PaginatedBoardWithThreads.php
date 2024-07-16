<?php

namespace App\DataTransferObjects;

readonly class PaginatedBoardWithThreads
{
    public function __construct(
        public string $boardName,
        public string $boardDescription,
        /** @var ThreadWithRecentReplies[] */
        public array $threads,
        public int $currentPage,
        public bool $hasMorePages,
        public int $lastPage,
        public int $totalItems
    ) {}
}
