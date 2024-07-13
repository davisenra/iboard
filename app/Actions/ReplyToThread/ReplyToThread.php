<?php

namespace App\Actions\ReplyToThread;

use App\Models\Post;
use App\Exception\PostException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class ReplyToThread
{
    public function handle(ReplyPayload $payload): void
    {
        /** @var ?Post $thread */
        $thread = Post::query()
            ->whereIsThread()
            ->where('id', $payload->threadId)
            ->first();

        if (!$thread || !$thread->isThread()) {
            throw PostException::threadNotFound($payload->threadId);
        }

        $hasFile = $payload->file !== null;
        $filename = $payload->file?->getFilename();

        if ($hasFile) {
            Storage::put(
                path: $filename,
                contents: $payload->file,
                options: 'public',
            );
        }

        $reply = new Post([
            'board_id' => $thread->board->id,
            'post_id' => $thread->id,
            'content' => $payload->content,
            'file' => $hasFile ? Storage::url($filename) : null,
        ]);

        $thread->bumpLastRepliedAt();

        DB::transaction(function () use ($reply, $thread) {
            $reply->save();
            $thread->update();
        });

        Log::info('Thread replied', [
            'thread_id' => $thread->id,
            'reply_id' => $reply->id,
        ]);
    }
}
