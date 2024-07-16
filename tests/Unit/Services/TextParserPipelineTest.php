<?php

namespace Tests\Unit\Services;

use App\Services\TextParserPipeline;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TextParserPipelineTest extends TestCase
{
    #[Test]
    public function testParseBold(): void
    {
        $content = 'This is **bold** text.';
        $parser = new TextParserPipeline($content);

        $parsedContent = $parser->parseBold()->getContent();

        $this->assertEquals('This is <strong>bold</strong> text.', $parsedContent);
    }

    #[Test]
    public function testParseItalic(): void
    {
        $content = 'This is *italic* text.';
        $parser = new TextParserPipeline($content);

        $parsedContent = $parser->parseItalic()->getContent();

        $this->assertEquals('This is <em>italic</em> text.', $parsedContent);
    }

    #[Test]
    public function testParseGreenText(): void
    {
        $content = '>This is greentext.';
        $parser = new TextParserPipeline($content);

        $parsedContent = $parser->parseGreenText()->getContent();

        $this->assertEquals('<span class="greentext">&gt;This is greentext.</span>', $parsedContent);
    }

    #[Test]
    public function testParseGreenTextWithMultipleLines(): void
    {
        $content = ">This is greentext\r\n>And more greentext\r\n>And even more greentext";
        $parser = new TextParserPipeline($content);

        $parsedContent = $parser->parseGreenText()->getContent();

        $expected = '<span class="greentext">&gt;This is greentext</span><br>'.
            '<span class="greentext">&gt;And more greentext</span><br>'.
            '<span class="greentext">&gt;And even more greentext</span>';

        $this->assertEquals($expected, $parsedContent);
    }

    #[Test]
    public function testParseSpoiler(): void
    {
        $content = 'This is a ||spoiler||.';
        $parser = new TextParserPipeline($content);

        $parsedContent = $parser->parseSpoiler()->getContent();

        $this->assertEquals('This is a <span class="spoiler">spoiler</span>.', $parsedContent);
    }

    #[Test]
    public function testParseReplyQuote(): void
    {
        $content = 'Check this out >>12345.';
        $parser = new TextParserPipeline($content);

        $parsedContent = $parser->parseReplyQuote()->getContent();

        $this->assertEquals('Check this out <a href="#12345">>>12345</a>.', $parsedContent);
    }

    #[Test]
    public function testReplyWithEverything(): void
    {
        $content = "This is **bold** text.\n".
            "This is *italic* text.\n".
            ">This is greentext.\n".
            "This is a ||spoiler||.\n".
            'Check this out >>12345.';

        $parser = new TextParserPipeline($content);

        $parsedContent = $parser
            ->parseBold()
            ->parseItalic()
            ->parseGreenText()
            ->parseSpoiler()
            ->parseReplyQuote()
            ->getContent();

        $expected = 'This is <strong>bold</strong> text.<br>'.
            'This is <em>italic</em> text.<br>'.
            '<span class="greentext">&gt;This is greentext.</span>'.'<br>'.
            'This is a <span class="spoiler">spoiler</span>.<br>'.
            'Check this out <a href="#12345">>>12345</a>.';

        $this->assertEquals($expected, $parsedContent);
    }

    #[Test]
    public function testNormalizeCarriageReturnNewLines(): void
    {
        $content = "First line.\r\nSecond line.\nThird line.";
        $parser = new TextParserPipeline($content);

        $parsedContent = $parser->getContent();

        $this->assertEquals('First line.<br>Second line.<br>Third line.', $parsedContent);
    }
}
