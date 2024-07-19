<?php

namespace Tests\Feature\Actions\ShowThread;

use App\Actions\ShowThread\ShowThread;
use App\Actions\ShowThread\ShowThreadPayload;
use App\DataTransferObjects\ThreadWithReplies;
use App\Exception\PostException;
use App\Models\Board;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowThreadTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function showThread(): void
    {
        $board = Board::factory()->create();

        $thread = Post::factory()
            ->for($board)
            ->thread()
            ->has(
                factory: Post::factory()
                    ->reply()
                    ->for($board)
                    ->count(5),
                relationship: 'replies'
            )
            ->create();

        $showThreadPayload = new ShowThreadPayload(threadId: $thread->id);

        $sut = new ShowThread();
        $threadWithReplies = $sut->handle($showThreadPayload);

        $this->assertInstanceOf(ThreadWithReplies::class, $threadWithReplies);
        $this->assertEquals($board->route, $threadWithReplies->boardRoute);
        $this->assertCount(5, $threadWithReplies->replies);
    }

    #[Test]
    public function showingNonExistentThread(): void
    {
        $this->expectException(PostException::class);
        $this->expectExceptionMessage('No thread found for id: 420');

        $showThreadPayload = new ShowThreadPayload(threadId: 420);

        $sut = new ShowThread();
        $sut->handle($showThreadPayload);
    }
}
