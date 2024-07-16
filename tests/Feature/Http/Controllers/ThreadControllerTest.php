<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Board;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ThreadControllerTest extends TestCase
{
    use DatabaseMigrations;
    use WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    #[Test]
    public function createNewThread(): void
    {
        $board = Board::factory()->create();

        $file = UploadedFile::fake()->createWithContent(
            name: 'image.jpg',
            content: file_get_contents(__DIR__.'/../../../Fixtures/image.jpeg') ?: throw new \RuntimeException('Could not load fixture image')
        );

        $response = $this->post("/$board->route/post", [
            'subject' => 'Foo',
            'content' => 'Bar',
            'file' => $file,
        ]);

        $thread = Post::first();

        $response->assertRedirectToRoute('thread.show', [
            'board' => $board->route,
            'thread' => $thread->id,
        ]);

        $this->assertDatabaseHas('posts', [
            'board_id' => $board->id,
            'subject' => 'Foo',
            'content' => 'Bar',
        ]);
    }
}
