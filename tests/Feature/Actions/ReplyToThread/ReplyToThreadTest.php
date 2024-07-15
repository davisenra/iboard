<?php

namespace Tests\Feature\Actions\ReplyToThread;

use App\Actions\ReplyToThread\ReplyPayload;
use App\Actions\ReplyToThread\ReplyToThread;
use App\Exception\PostException;
use App\Models\Board;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use SplFileObject;
use Tests\TestCase;

class ReplyToThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    #[Test]
    public function replyToThreadWithImage(): void
    {
        $board = Board::factory()->create();

        $thread = Post::create([
            'board_id' => $board->id,
            'subject' => 'Foo',
            'content' => 'Bar',
            'file' => 'foo.jpg',
        ]);

        $file = new SplFileObject(__DIR__.'/../../../Fixtures/image.jpeg', 'r');

        $payload = new ReplyPayload(
            threadId: $thread->id,
            content: 'Foo',
            file: $file,
        );

        $sut = new ReplyToThread();
        $sut->handle($payload);

        Storage::disk('local')->exists($file->getFilename());

        $this->assertDatabaseHas('posts', [
            'post_id' => $payload->threadId,
            'content' => $payload->content,
            'file' => Storage::url($file->getFilename()),
        ]);
    }

    #[Test]
    public function replyToThreadWithoutImage(): void
    {
        $board = Board::factory()->create();
        $thread = Post::factory()->for($board)->create();

        $payload = new ReplyPayload(
            threadId: $thread->id,
            content: 'Foo',
            file: null,
        );

        $sut = new ReplyToThread();
        $sut->handle($payload);

        $this->assertDatabaseHas('posts', [
            'post_id' => $payload->threadId,
            'content' => $payload->content,
            'file' => null,
        ]);
    }

    #[Test]
    public function replyWithSageDoesNotBumpTheThread(): void
    {
        $board = Board::factory()->create();
        $thread = Post::factory()->for($board)->create();

        $payload = new ReplyPayload(
            threadId: $thread->id,
            content: 'Foo',
            options: 'sage',
            file: null
        );

        $sut = new ReplyToThread();
        $sut->handle($payload);

        $this->assertDatabaseHas('posts', [
            'post_id' => $payload->threadId,
            'last_replied_at' => null,
        ]);
    }

    #[Test]
    public function exceptionIsThrownWhenReplyingToNonExistentThread(): void
    {
        $payload = new ReplyPayload(
            threadId: 420,
            content: 'Foo',
            file: null,
        );

        $this->expectException(PostException::class);
        $this->expectExceptionMessage('No thread found for id: 420');

        $sut = new ReplyToThread();
        $sut->handle($payload);
    }
}
