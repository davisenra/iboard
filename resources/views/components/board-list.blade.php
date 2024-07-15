<div class="bg-blue-200 border-b border-blue-300 flex space-x-2 text-sm px-2">
    <span class="opacity-20">&#91;</span>
    @foreach ($boards as $board)
        <a
            class="hover:text-red-600"
            href="{{ $board->route }}"
            title="{{ $board->title }}"
        >
            {{ $board->route }}
        </a>
        @if ($loop->odd)
            <span class="opacity-20">&#47;</span>
        @endif
    @endforeach
    <span class="opacity-20">&#93;</span>
</div>
