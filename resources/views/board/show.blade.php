<x-layout>
    <header class="py-3 space-y-3 flex flex-col justify-center items-center w-full">
        <h1 class="font-bold text-2xl text-red-600">
            &#47;{{ $boardRoute }}&#47; - {{ $boardName }}
        </h1>
        <hr class="w-full my-3" />
        <form class="space-y-0.5" action="{{ route('thread.store', [$boardRoute]) }}" method="post"
              enctype="multipart/form-data">
            @csrf
            <div class="flex text-sm">
                <label class="bg-sky-500 border border-black px-1 w-24 font-bold" for="name">Name</label>
                <input class="ml-0.5 border border-neutral-200 bg-white px-1" type="text" name="name"
                       id="name"
                       value="Anonymous"
                       disabled>
            </div>
            <div class="flex text-sm">
                <label class="bg-sky-500 border border-black px-1 w-24 font-bold" for="name">Options</label>
                <input class="ml-0.5 border border-neutral-200 bg-white px-1" type="text" name="options"
                       id="options">
            </div>
            <div class="flex text-sm">
                <label class="bg-sky-500 border border-black px-1 w-24 font-bold" for="name">Subject</label>
                <input class="ml-0.5 border border-neutral-200 bg-white px-1" type="text" name="subject"
                       id="subject">
                <button class="ml-0.5 px-2 bg-neutral-200 border border-neutral-600 rounded-sm" type="submit">
                    Post
                </button>
            </div>
            <div class="flex text-sm">
                <label class="bg-sky-500 border border-black px-1 w-24 font-bold" for="name">Comment</label>
                <textarea class="ml-0.5 border border-neutral-200 bg-white w-72 h-24"
                          name="content"
                          id="content"></textarea>
            </div>
            <div class="flex text-sm">
                <label class="bg-sky-500 border border-black px-1 w-24 font-bold" for="file">File</label>
                <input class="ml-0.5 bg-white border border-neutral-200" type="file" name="file"
                       id="file">
            </div>
        </form>
        <hr class="my-3 w-full" />
    </header>
    <main class="mx-4 flex flex-col space-y-3 lg:mx-6">
        @foreach($threads as $thread)
            <div x-data="{ collapseThread: false }"
                 class="flex text-sm max-w-7xl" :class="!collapseThread ? 'flex-col' : ''">
                <div class="flex space-x-2">
                    <button x-on:click="collapseThread = !collapseThread" class="hover:text-red-600"
                            x-text="collapseThread ? '[+]' : '[-]'">
                    </button>
                    <p>
                        File:
                        <a class="underline hover:text-red-600" href="{{ $thread->file->file }}" target="_blank">
                            {{ $thread->file->originalFilename }}
                        </a>
                        ({{ $thread->file->getHumanReadableFileSize() }}, {{$thread->file->resolution}})
                    </p>
                </div>
                <div class="flex space-x-2">
                    <img
                        x-show="!collapseThread"
                        class="max-w-xs"
                        src="{{ $thread->file->file }}"
                        alt=""
                    >
                    <div class="flex flex-col">
                        <div class="flex space-x-1">
                            <p class="text-slate-800 font-bold">{{ $thread->subject  }}</p>
                            <p class="text-emerald-600 font-bold">Anonymous</p>
                            <p>{{ $thread->publishedAt->format('d/m/y (D) H:i:s') }}</p>
                            <p>No.
                                <a class="hover:text-red-600"
                                   href="{{ route('thread.show', [$boardRoute, $thread->threadId]) }}">
                                    {{ $thread->threadId }}
                                </a>
                            </p>
                            <p>
                                &#91;
                                <a class="hover:text-red-600"
                                   href="{{ route('thread.show', [$boardRoute, $thread->threadId]) }}">Reply</a>
                                &#93;
                            </p>
                        </div>
                        <p x-show="!collapseThread">{!! $thread->content !!}</p>
                    </div>
                </div>
                <div x-show="!collapseThread" class="mt-3 flex flex-col space-y-3 max-w-6xl">
                    @foreach($thread->recentReplies as $reply)
                        <div id="{{ $reply->replyId }}" class="flex flex-col p-3 space-x-2 bg-blue-100">
                            @if ($reply->file)
                                <p>
                                    File:
                                    <a class="underline hover:text-red-600" href="{{ $thread->file->file }}"
                                       target="_blank">
                                        {{ $thread->file->originalFilename }}
                                    </a>
                                    ({{ $thread->file->getHumanReadableFileSize() }}, {{$thread->file->resolution}})
                                </p>
                            @endif
                            <div class="flex space-x-2">
                                @if ($reply->file)
                                    <img
                                        class="max-w-xs"
                                        src="{{ $reply->file->file }}"
                                        alt=""
                                    >
                                @endif
                                <div>
                                    <div class="flex space-x-1">
                                        <p class="text-emerald-600 font-bold">Anonymous</p>
                                        <p>{{ $reply->publishedAt->format('d/m/y (D) H:i:s') }}</p>
                                        <p>
                                            No.
                                            <a class="hover:text-red-600"
                                               href="{{ route('thread.show', [$boardRoute, $thread->threadId, "#$reply->replyId"]) }}">
                                                {{ $reply->replyId }}
                                            </a>
                                        </p>
                                    </div>
                                    <p class="ml-4 mt-2">{!! $reply->content !!}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <hr class="my-2" />
        @endforeach
        <footer class="pb-3">
            <div class="flex bg-blue-100 border border-blue-200 w-max space-x-1.5 py-1 px-2 text-sm">
                @for ($i = 1; $i <= $lastPage; $i++)
                    <p>
                        &#91;
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                           class="{{ $currentPage == $i ? 'font-bold' : '' }} hover:text-red-600">{{ $i }}</a>
                        &#93;
                    </p>
                @endfor
            </div>
        </footer>
    </main>
</x-layout>
