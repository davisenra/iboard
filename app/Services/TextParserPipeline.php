<?php

namespace App\Services;

class TextParserPipeline
{
    public function __construct(
        private string $content
    ) {
        $this->normalizeCarriageReturnNewLines();
    }

    private function normalizeCarriageReturnNewLines(): void
    {
        $this->content = preg_replace(
            pattern: '/\r\n|\r|\n/',
            replacement: "\n",
            subject: $this->content
        );
    }

    private function replaceNewLinesWithBreakpoints(): void
    {
        $this->content = str_replace("\n", '<br>', $this->content);
    }

    public function parseBold(): self
    {
        $this->content = preg_replace(
            pattern: '/\*\*(.*?)\*\*/',
            replacement: '<strong>$1</strong>',
            subject: $this->content
        );

        return $this;
    }

    public function parseItalic(): self
    {
        $this->content = preg_replace(
            pattern: '/\*(.*?)\*/',
            replacement: '<em>$1</em>',
            subject: $this->content
        );

        return $this;
    }

    public function parseGreenText(): self
    {
        $this->content = preg_replace(
            pattern: '/^>(.*)$/m',
            replacement: '<span class="greentext">&gt;$1</span>',
            subject: $this->content
        );

        return $this;
    }

    public function parseSpoiler(): self
    {
        $this->content = preg_replace(
            pattern: '/\|\|(.*?)\|\|/',
            replacement: '<span class="spoiler">$1</span>',
            subject: $this->content
        );

        return $this;
    }

    public function parseReplyQuote(): self
    {
        $this->content = preg_replace(
            pattern: '/>>(\d+)/',
            replacement: '<a href="#$1">>>$1</a>',
            subject: $this->content
        );

        return $this;
    }

    public function getContent(): string
    {
        $this->replaceNewLinesWithBreakpoints();

        return $this->content;
    }
}
