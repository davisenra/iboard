<?php

namespace App\Actions\NewThread;

use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class CreateNewThread
{
    public function __construct(
        private NewThreadPayload $payload,
    ) {}

    public function handle(): void
    {
        $filename = $this->payload->file->getFilename();

        Storage::put(
            path: $filename,
            contents: $this->payload->file,
            options: 'public',
        );

        $thread = new Post([
            'board_id' => $this->payload->boardId,
            'subject' => $this->payload->subject,
            'content' => $this->payload->content,
            'file' => Storage::url($filename),
            'last_replied_at' => now(),
        ]);

        $thread->save();

        Log::info('Thread created', [
            'board_id' => $this->payload->boardId,
            'subject' => $this->payload->subject,
        ]);
    }
}
