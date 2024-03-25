<?php
use PHPUnit\Framework\TestCase;

class AnchorIDGenerationTest extends TestCase
{
    protected $parsedownToc;

    protected function setUp(): void
    {
        $this->parsedownToc = new ParsedownToc();
        $this->parsedownToc->setSafeMode(true);
    }

    /**
     * Test case for generating unique anchor IDs without duplicates.
     */
    public function testAnchorID()
    {
        $text = "uniqueheading";
        $this->assertEquals('uniqueheading', $this->invokeMethod($this->parsedownToc, 'createAnchorID', [$text]));
    }

    /**
     * Test case for checking the incrementation of duplicate anchor IDs.
     *
     * @return void
     */
    public function testAnchorIDDuplicate()
    {
        $text = "heading";
        $this->parsedownToc->setOptions(['blacklist' => []]); // Ensure no blacklist interference

        $firstCall = $this->invokeMethod($this->parsedownToc, 'createAnchorID', [$text]);
        $secondCall = $this->invokeMethod($this->parsedownToc, 'createAnchorID', [$text]);

        $this->assertNotEquals($firstCall, $secondCall);
        $this->assertTrue(strpos($secondCall, $firstCall . '-') !== false);
        $this->assertEquals('heading', $firstCall);
        $this->assertEquals('heading-1', $secondCall);
    }

    /**
     * Test case for custom anchor ID generation callback.
     *
     * This test verifies that the custom anchor ID generation callback is correctly set and applied.
     * It checks if the generated HTML contains the expected anchor ID based on the custom function.
     */
    public function testAnchorIDCustomCallback()
    {
        $customFunction = function($text, $options) {
            return mb_strtolower(str_replace(' ', '_', $text));
        };
        $this->parsedownToc->setCreateAnchorIDCallback($customFunction);

        $markdown = "# custom heading";
        $html = $this->parsedownToc->text($markdown);

        $this->assertStringContainsString('id="custom_heading"', $html);
    }

    /**
     * Test case for generating anchor IDs with blacklist.
     *
     * This test verifies that the createAnchorID method of the ParsedownToc class
     * generates the correct anchor ID when a blacklist is set and the input text
     * matches an item in the blacklist.
     */
    public function testAnchorIDBlacklist()
    {
        $text = "heading";
        $this->parsedownToc->setOptions(['blacklist' => ['heading']]);

        $result = $this->invokeMethod($this->parsedownToc, 'createAnchorID', [$text]);
        $this->assertNotEquals('heading', $result);
        $this->assertEquals('heading-1', $result);
    }

    /**
     * Test case for anchor ID selectors.
     */
    public function testAnchorIDSelectors()
    {
        $this->parsedownToc->setOptions(['selectors' => ['h1']]);

        $text = "# heading1";
        $this->parsedownToc->text($text);
        $result = $this->parsedownToc->contentsList('html');
        $this->assertStringContainsString('<a href="#heading1">heading1</a>', $result);

        $text = "## heading2";
        $this->parsedownToc->text($text);
        $result = $this->parsedownToc->contentsList('html');
        $this->assertStringNotContainsString('<a href="#heading2">heading2</a>', $result);
    }

    /**
     * This test case verifies the behavior of the sanitizeAnchor method by testing various input scenarios.
     */
    public function testAnchorIDSanitizeAnchor()
    {
        $this->parsedownToc->setOptions(['delimiter' => '_']);
        
        $text = "heading";
        $result = $this->invokeMethod($this->parsedownToc, 'sanitizeAnchor', [$text]);
        $this->assertEquals('heading', $result);

        $text = "heading with spaces";
        $result = $this->invokeMethod($this->parsedownToc, 'sanitizeAnchor', [$text]);
        $this->assertEquals('heading_with_spaces', $result);

        $text = "heading with special xxxxxxxxxxx@xxxxxxxx";
        $result = $this->invokeMethod($this->parsedownToc, 'sanitizeAnchor', [$text]);
        $this->assertEquals('heading_with_special_xxxxxxxxxxx_xxxxxxxx', $result);
    }


    /**
     * Invokes a protected or private method of an object using reflection.
     *
     * @param object $object The object whose method needs to be invoked.
     * @param string $methodName The name of the method to be invoked.
     * @param array $parameters An array of parameters to be passed to the method.
     * @return mixed The result of the method invocation.
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    protected function tearDown(): void
    {
        unset($this->parsedownToc);
    }
}
