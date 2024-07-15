<?php

namespace Tests\Feature\Actions\NewThread;

use App\Actions\NewThread\CreateNewThread;
use App\Actions\NewThread\NewThreadPayload;
use App\Models\Board;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use SplFileObject;
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

        $file = new SplFileObject(__DIR__.'/../../../Fixtures/image.jpeg', 'r');

        $payload = new NewThreadPayload(
            boardId: $board->id,
            subject: 'Foo',
            content: 'Bar',
            file: $file,
        );

        $sut = new CreateNewThread();
        $sut->handle($payload);

        Storage::disk('local')->exists($file->getFilename());

        $this->assertDatabaseHas('posts', [
            'board_id' => $payload->boardId,
            'subject' => $payload->subject,
            'content' => $payload->content,
            'file' => Storage::url($file->getFilename()),
        ]);
    }
}
