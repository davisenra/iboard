<?php

namespace App\Actions\NewThread;

use Illuminate\Http\UploadedFile;

readonly class NewThreadPayload
{
    public function __construct(
        public string $boardRoute,
        public ?string $subject,
        public string $content,
        public UploadedFile $file,
    ) {}
}
