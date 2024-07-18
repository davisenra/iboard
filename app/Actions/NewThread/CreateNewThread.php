<?php

namespace App\Actions\NewThread;

use App\DataTransferObjects\NewlyCreatedThread;
use App\Exception\BoardException;
use App\Models\Board;
use App\Models\Post;
use App\Services\TextParserPipeline;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class CreateNewThread
{
    public function handle(NewThreadPayload $payload): NewlyCreatedThread
    {
        $board = Board::where('route', $payload->boardRoute)->first();

        if (! $board) {
            throw BoardException::notFound($payload->boardRoute);
        }

        $fileUrl = $this->storeFile($payload->file);
        $imageSize = $this->getImageSize($payload->file);

        $content = $this->parseContent($payload->content);

        $thread = new Post([
            'board_id' => $board->id,
            'subject' => $payload->subject ?: null,
            'content' => $content,
            'file' => $fileUrl,
            'file_size' => $payload->file->getSize() ?: null,
            'file_resolution' => $imageSize ? sprintf('%sx%s', $imageSize[0], $imageSize[1]) : null,
            'original_filename' => $payload->file->getClientOriginalName() ?: null,
            'ip_address' => $payload->ipAddress,
            'last_replied_at' => now(),
        ]);

        $thread->save();

        Log::info('Thread created', [
            'board_id' => $payload->boardRoute,
            'subject' => $payload->subject,
        ]);

        return new NewlyCreatedThread($thread->id);
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

    /**
     * @return array{0: int, 1: int}|null
     */
    private function getImageSize(UploadedFile $file): ?array
    {
        return getimagesize($file->getPath().'/'.$file->getFilename()) ?: null;
    }
}
