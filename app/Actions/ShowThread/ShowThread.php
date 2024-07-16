<?php

namespace App\Actions\ShowThread;

use App\DataTransferObjects\Reply;
use App\DataTransferObjects\ThreadWithReplies;
use App\Exception\PostException;
use App\Models\Post;

class ShowThread
{
    public function handle(ShowThreadPayload $payload): ThreadWithReplies
    {
        $thread = Post::query()
            ->where('id', '=', $payload->threadId)
            ->with('replies')
            ->first();

        if (! $thread) {
            throw PostException::threadNotFound($payload->threadId);
        }

        $replies = $thread
            ->replies
            ->map(function (Post $reply) {
                return new Reply(
                    replyId: $reply->id,
                    threadId: $reply->post_id,
                    content: $reply->content,
                    options: $reply->options,
                    file: $reply->file,
                    publishedAt: $reply->published_at->toDateTimeImmutable(),
                );
            });

        return new ThreadWithReplies(
            boardRoute: $thread->board->route,
            boardName: $thread->board->name,
            threadId: $thread->id,
            subject: $thread->subject,
            content: $thread->content,
            file: $thread->file,
            publishedAt: $thread->published_at->toDateTimeImmutable(),
            lastRepliedAt: $thread->last_replied_at->toDateTimeImmutable(),
            replies: $replies->toArray()
        );
    }
}
