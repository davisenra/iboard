<?php

namespace App\Actions\ReplyToThread;

use App\Exception\PostException;
use App\Models\Post;
use App\Services\TextParserPipeline;
use Illuminate\Http\UploadedFile;
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

        if (! $thread || ! $thread->isThread()) {
            throw PostException::threadNotFound($payload->threadId);
        }

        $hasFile = $payload->file !== null;

        if ($hasFile) {
            $fileUrl = $this->storeFile($payload->file);
        }

        $content = $this->parseContent($payload->content);

        $reply = new Post([
            'board_id' => $thread->board->id,
            'post_id' => $thread->id,
            'content' => $content,
            'file' => $fileUrl ?? null,
        ]);

        if (! $payload->isSage()) {
            $thread->bumpLastRepliedAt();
        }

        DB::transaction(function () use ($reply, $thread) {
            $reply->save();
            $thread->update();

            Log::info('Thread replied', [
                'thread_id' => $thread->id,
                'reply_id' => $reply->id,
            ]);
        });
    }

    private function storeFile(UploadedFile $file): string
    {
        $filename = sprintf(
            '%s.%s',
            mb_substr(sha1((string) time()), 16),
            $file->getClientOriginalExtension()
        );

        Storage::putFileAs(
            path: 'public',
            file: $file,
            name: $filename,
            options: 'public',
        );

        return Storage::url($filename);
    }

    private function parseContent(string $content): string
    {
        return (new TextParserPipeline($content))
            ->parseBold()
            ->parseItalic()
            ->parseSpoiler()
            ->parseReplyQuote()
            ->parseGreenText()
            ->getContent();
    }
}
