<?php

namespace App\Http\Controllers;

use App\Actions\NewThread\CreateNewThread;
use App\Actions\NewThread\NewThreadPayload;
use App\Actions\ReplyToThread\ReplyPayload;
use App\Actions\ReplyToThread\ReplyToThread;
use App\Actions\ShowThread\ShowThread;
use App\Actions\ShowThread\ShowThreadPayload;
use App\Exception\PostException;
use App\Http\Requests\StoreReplyRequest;
use App\Http\Requests\StoreThreadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ThreadController extends Controller
{
    public function show(string $_, string $threadId): View
    {
        $payload = new ShowThreadPayload((int) $threadId);
        $handler = new ShowThread();

        try {
            $threadWithReplies = $handler->handle($payload);
        } catch (PostException) {
            abort(404);
        }

        return view('thread.show', [
            'boardRoute' => $threadWithReplies->boardRoute,
            'boardName' => $threadWithReplies->boardName,
            'thread' => $threadWithReplies,
        ]);
    }

    public function store(string $boardRoute, StoreThreadRequest $request): RedirectResponse
    {
        $data = $request->validated();
        /** @var UploadedFile $file */
        $file = $request->file('file');

        $payload = new NewThreadPayload(
            boardRoute: $boardRoute,
            subject: $data['subject'],
            content: $data['content'],
            ipAddress: $request->server->get('REMOTE_ADDR'),
            file: $file,
        );

        $handler = new CreateNewThread();
        $newlyCreatedThread = $handler->handle($payload);

        return Redirect::route('thread.show', [
            'board' => $boardRoute,
            'thread' => $newlyCreatedThread->threadId,
        ]);
    }

    public function reply(string $boardRoute, string $threadId, StoreReplyRequest $request): RedirectResponse
    {
        $data = $request->validated();
        /** @var ?UploadedFile $file */
        $file = $request->file('file');

        $payload = new ReplyPayload(
            threadId: (int) $threadId,
            content: $data['content'],
            ipAddress: $request->server->get('REMOTE_ADDR'),
            options: $data['options'] ?? null,
            file: $file,
        );

        $handler = new ReplyToThread();
        try {
            $handler->handle($payload);
        } catch (PostException) {
            abort(404);
        }

        return Redirect::route('thread.show', [
            'board' => $boardRoute,
            'thread' => $threadId,
        ]);
    }
}
