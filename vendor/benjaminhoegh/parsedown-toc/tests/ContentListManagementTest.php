<?php
use PHPUnit\Framework\TestCase;

class ContentListManagementTest extends TestCase
{
    protected $parsedownToc;

    protected function setUp(): void
    {
        $this->parsedownToc = new ParsedownToc();
        $this->parsedownToc->setSafeMode(true);
    }

    
    public function testContentsListString()
    {
        $markdown = "Some content\n\n# Heading 1\n\n## Heading 1.1\n\n# Heading 2\n\n## Heading 2.1";
        $this->parsedownToc->text($markdown); // Process markdown to generate TOC
        $result = $this->parsedownToc->contentsList('string');
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        
        // Also check that we can use html as an alias for string
        $result = $this->parsedownToc->contentsList('html');
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testContentsListReturnsJson()
    {
        $markdown = "Some content\n\n# Heading 1\n\n## Heading 1.1\n\n# Heading 2\n\n## Heading 2.1";
        $this->parsedownToc->text($markdown); // Process markdown to generate TOC
        $result = $this->parsedownToc->contentsList('json');
        $this->assertIsString($result);
        $this->assertJson($result);
    }
    
    public function testContentsListArray()
    {
        $markdown = "Some content\n\n# Heading 1\n\n## Heading 1.1\n\n# Heading 2\n\n## Heading 2.1";
        $this->parsedownToc->text($markdown); // Process markdown to generate TOC
        $result = $this->parsedownToc->contentsList('array');
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function testContentsListReturnsHtmlByDefault()
    {
        $markdown = "Some content\n\n# Heading 1\n\n## Heading 1.1\n\n# Heading 2\n\n## Heading 2.1";
        $this->parsedownToc->text($markdown); // Process markdown to generate TOC
        $result = $this->parsedownToc->contentsList();
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testContentsListInvalidType()
    {
        $markdown = "Some content\n\n# Heading 1\n\n## Heading 1.1\n\n# Heading 2\n\n## Heading 2.1";
        $this->parsedownToc->text($markdown); // Process markdown to generate TOC
        $this->expectException(InvalidArgumentException::class);
        $this->parsedownToc->contentsList('invalid');
    }

    protected function tearDown(): void
    {
        unset($this->parsedownToc);
    }
}
