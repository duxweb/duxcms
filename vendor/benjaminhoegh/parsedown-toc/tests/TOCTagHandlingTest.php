<?php
use PHPUnit\Framework\TestCase;

class TOCTagHandlingTest extends TestCase
{
    protected $parsedownToc;

    protected function setUp(): void
    {
        $this->parsedownToc = new ParsedownToc();
        $this->parsedownToc->setSafeMode(true);
    }

    public function testTOCTagReplacement()
    {
        $markdownWithTOC = "Some content\n\n[toc]\n\nMore content";
        $output = $this->parsedownToc->text($markdownWithTOC);
        // Check if $output contains the expected TOC div with the id set in options
        $this->assertStringContainsString('<div id="toc">', $output);
        // Further checks can verify the correctness of the TOC content itself
    }

    protected function tearDown(): void
    {
        unset($this->parsedownToc);
    }
}
