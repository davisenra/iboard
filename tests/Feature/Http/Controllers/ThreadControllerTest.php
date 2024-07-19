<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\ThreadController;
use App\Models\Board;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ThreadController::class)]
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

        $file = $this->getFixtureFileForUpload();

        $response = $this->post("/$board->route/post", [
            'subject' => 'Foo',
            'content' => 'Bar',
            'file' => $file,
        ]);

        $thread = Post::first();

        $this->assertEquals(302, $response->getStatusCode());

        $response->assertRedirectToRoute('thread.show', [
            'board' => $board->route,
            'thread' => $thread->id,
        ]);

        $this->assertNotNull($thread);
        $this->assertEquals($thread->board->id, $board->id);
        $this->assertNull($thread->post_id);
        $this->assertEquals('Bar', $thread->content);
        $this->assertEquals('Foo', $thread->subject);
        $this->assertNotNull($thread->file);
        $this->assertNotNull($thread->file_size);
        $this->assertEquals('image.jpeg', $thread->original_filename);
        $this->assertEquals('20x20', $thread->file_resolution);
    }

    #[Test]
    public function replyToThreadWithImage(): void
    {
        $board = Board::factory()->create();

        $thread = Post::factory()
            ->for($board)
            ->thread()
            ->create();

        $file = $this->getFixtureFileForUpload();

        $response = $this->post("/$board->route/$thread->id/reply", [
            'content' => 'Bar',
            'options' => null,
            'file' => $file,
        ]);

        $this->assertEquals(302, $response->getStatusCode());

        $response->assertRedirectToRoute('thread.show', [
            'board' => $board->route,
            'thread' => $thread->id,
        ]);

        $reply = $thread->replies()->first();

        $this->assertNotNull($reply);
        $this->assertEquals($thread->id, $reply->post_id);
        $this->assertNotNull($reply->content);
        $this->assertNotNull($reply->ip_address);
        $this->assertNotNull($reply->file);
        $this->assertNotNull($reply->file_size);
        $this->assertNotNull($reply->original_filename);
        $this->assertNotNull($reply->file_resolution);
    }

    #[Test]
    public function replyToThreadWithoutImage(): void
    {
        $board = Board::factory()->create();

        $thread = Post::factory()
            ->for($board)
            ->thread()
            ->create();

        $response = $this->post("/$board->route/$thread->id/reply", [
            'content' => 'Bar',
            'options' => null,
        ]);

        $this->assertEquals(302, $response->getStatusCode());

        $response->assertRedirectToRoute('thread.show', [
            'board' => $board->route,
            'thread' => $thread->id,
        ]);

        $reply = $thread->replies()->first();

        $this->assertNotNull($reply);
        $this->assertEquals($thread->id, $reply->post_id);
        $this->assertNotNull($reply->content);
        $this->assertNotNull($reply->ip_address);
        $this->assertNull($reply->file);
        $this->assertNull($reply->file_size);
        $this->assertNull($reply->original_filename);
        $this->assertNull($reply->file_resolution);
    }

    #[Test]
    public function viewingThread(): void
    {
        $board = Board::factory()->create();

        $thread = Post::factory()
            ->for($board)
            ->thread()
            ->create();

        $response = $this->get("/$board->route/$thread->id");

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function notFondWhenThreadDoesNotExist(): void
    {
        $board = Board::factory()->create();

        $response = $this->get("/$board->route/420");

        $this->assertEquals(404, $response->getStatusCode());
    }

    private function getFixtureFileForUpload(): File
    {
        return UploadedFile::fake()->createWithContent(
            name: 'image.jpeg',
            content: file_get_contents(__DIR__.'/../../../Fixtures/image.jpeg') ?: throw new \RuntimeException('Could not load fixture image')
        );
    }
}
