<?php

namespace Tests\Feature\Actions\NewThread;

use App\Actions\NewThread\CreateNewThread;
use App\Actions\NewThread\NewThreadPayload;
use App\Models\Board;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateNewThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    #[Test]
    public function createNewThread(): void
    {
        $board = Board::factory()->create();

        $file = new UploadedFile(__DIR__.'/../../../Fixtures/image.jpeg', 'image.jpeg');

        $payload = new NewThreadPayload(
            boardRoute: $board->route,
            subject: 'Foo',
            content: 'Bar',
            file: $file,
        );

        $sut = new CreateNewThread();
        $sut->handle($payload);

        Storage::disk('local')->exists($file->getFilename());

        $this->assertDatabaseHas('posts', [
            'board_id' => $board->id,
            'subject' => $payload->subject,
            'content' => $payload->content,
        ]);
    }
}
