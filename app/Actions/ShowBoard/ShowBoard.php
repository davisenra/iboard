<?php

namespace App\Actions\ShowBoard;

use App\DataTransferObjects\PaginatedBoardWithThreads;
use App\DataTransferObjects\Reply;
use App\DataTransferObjects\ThreadWithRecentReplies;
use App\Exception\BoardException;
use App\Models\Board;

class ShowBoard
{
    private const int PER_PAGE = 10;

    /**
     * @throws BoardException
     */
    public function handle(ShowBoardPayload $payload): PaginatedBoardWithThreads
    {
        $board = Board::where('route', $payload->boardRoute)->first();

        if (! $board) {
            throw BoardException::notFound($payload->boardRoute);
        }

        $paginatedThreads = $board->threads()
            ->with(['replies' => function ($query) {
                $query
                    ->select(['id', 'post_id', 'content', 'file', 'options', 'published_at'])
                    ->orderBy('published_at', 'desc')
                    ->take(3);
            }])
            ->select(['id', 'subject', 'content', 'file', 'published_at', 'last_replied_at'])
            ->orderBy('last_replied_at', 'desc')
            ->paginate(self::PER_PAGE, ['*'], 'page', $payload->page);

        $threads = $paginatedThreads->map(function ($thread) {
            $recentReplies = $thread->replies->map(function ($reply) {
                return new Reply(
                    replyId: $reply->id,
                    threadId: $reply->post_id,
                    content: $reply->content,
                    options: $reply->options,
                    file: $reply->file,
                    publishedAt: $reply->published_at->toDateTimeImmutable(),
                );
            });

            return new ThreadWithRecentReplies(
                threadId: $thread->id,
                subject: $thread->subject,
                content: $thread->content,
                file: $thread->file,
                publishedAt: $thread->published_at->toDateTimeImmutable(),
                lastRepliedAt: $thread->last_replied_at->toDateTimeImmutable(),
                recentReplies: $recentReplies->toArray()
            );
        });

        return new PaginatedBoardWithThreads(
            boardName: $board->name,
            boardDescription: $board->description,
            threads: $threads->toArray(),
            currentPage: $paginatedThreads->currentPage(),
            hasMorePages: $paginatedThreads->currentPage() !== $paginatedThreads->lastPage(),
            lastPage: $paginatedThreads->lastPage(),
            totalItems: $paginatedThreads->total()
        );
    }
}