<?php

namespace App\DataTransferObjects;

use App\Models\Post;

readonly class File
{
    public function __construct(
        public string $file,
        public int $sizeInBytes,
        public string $originalFilename,
        public string $resolution
    ) {}

    public static function fromPost(Post $post): self
    {
        return new File(
            file: $post->file,
            sizeInBytes: $post->file_size,
            originalFilename: $post->original_filename,
            resolution: $post->file_resolution,
        );
    }

    public function getHumanReadableFileSize(): string
    {
        $bytes = $this->sizeInBytes;

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        $kb = $bytes / 1024;
        if ($kb < 1024) {
            return round($kb, 2).' KB';
        }

        $mb = $kb / 1024;

        return round($mb, 2).' MB';
    }
}
