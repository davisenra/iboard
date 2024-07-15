<?php

namespace App\Actions\NewThread;

use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class CreateNewThread
{
    public function handle(NewThreadPayload $payload): void
    {
        $filename = $payload->file->getFilename();

        Storage::put(
            path: $filename,
            contents: $payload->file,
            options: 'public',
        );

        $thread = new Post([
            'board_id' => $payload->boardId,
            'subject' => $payload->subject ?: null,
            'content' => $payload->content,
            'file' => Storage::url($filename),
            'last_replied_at' => now(),
        ]);

        $thread->save();

        Log::info('Thread created', [
            'board_id' => $payload->boardId,
            'subject' => $payload->subject,
        ]);
    }
}
