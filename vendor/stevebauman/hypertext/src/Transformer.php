<?php

namespace Stevebauman\Hypertext;

use Closure;
use DOMDocument;
use DOMXPath;
use HTMLPurifier;
use HTMLPurifier_Config;

class Transformer
{
    /**
     * Filter HTML for specific element(s) using XPath expression.
     */
    protected ?string $filter = null;

    /**
     * Whether to keep anchor tags in the output.
     */
    protected bool $keepLinks = false;

    /**
     * Whether to keep new lines in the output.
     */
    protected bool $keepNewLines = false;

    /**
     * The various unicode spaces to replace with single spaces.
     */
    protected array $spaces = [
        "\u{00AD}", // Soft Hyphen
        "\u{200B}", // Zero Width Space
        "\u{200C}", // Zero Width Non-Joiner
        "\u{200D}", // Zero Width Joiner
        "\u{200E}", // Left-To-Right Mark
        "\u{200F}", // Right-To-Left Mark
        "\u{FEFF}", // Zero Width No-Break Space (Byte Order Mark)
        "\u{2060}", // Word Joiner
        "\u{2002}", // En Space
        "\u{2003}", // Em Space
        "\u{2004}", // Three-Per-Em Space
        "\u{2005}", // Four-Per-Em Space
        "\u{2006}", // Six-Per-Em Space
        "\u{2007}", // Figure Space
        "\u{2008}", // Punctuation Space
        "\u{2009}", // Thin Space
        "\u{200A}", // Hair Space
        "\u{00A0}", // Non-breaking Space
        "\u{202F}", // Narrow No-Break Space
        "\u{205F}", // Medium Mathematical Space
        "\u{3000}", // Ideographic Space
        "\u{034F}", // Combining Grapheme Joiner (CGJ)
    ];

    /**
     * Set an XPath to filter HTML for specific element(s).
     */
    public function filter($xPath): static
    {
        $this->filter = $xPath;

        return $this;
    }

    /**
     * Enable keeping anchor tags with their href in the output.
     */
    public function keepLinks(): static
    {
        $this->keepLinks = true;

        return $this;
    }

    /**
     * Enable keeping new lines in the output.
     */
    public function keepNewLines(): static
    {
        $this->keepNewLines = true;

        return $this;
    }

    /**
     * Transform the HTML into text.
     */
    public function toText(string $html): string
    {
        return array_reduce($this->pipeline(), fn (string $html, Closure $transformer) => (
            $transformer($html)
        ), $html);
    }

    /**
     * Get the HTML-to-text pipeline.
     */
    protected function pipeline(): array
    {
        return array_merge(
            $this->filter ? [
                // Query the HTML for specific element(s) using an XPath expression.
                fn (string $html) => $this->query($html)
            ] : [],
            [
                // Convert any quoted-printable strings to an 8 bit string.
                fn (string $html) => quoted_printable_decode($html),

                // Add spacing between HTML tags.
                fn (string $html) => preg_replace('/(>)(<)/', '$1 $2', $html),

                // Remove various forms of unneeded spaces.
                fn (string $html) => str_replace($this->spaces, ' ', $html),

                // Strip all CSS, HTML tags, and scripts.
                fn (string $html) => $this->makePurifier()->purify($html),

                // Strip all remaining HTML tags after purification.
                fn (string $html) => strip_tags($html, $this->keepLinks ? '<a>' : null),

                // Remove all horizontal spaces.
                fn (string $html) => preg_replace( '/\h+/u', ' ', $html),

                // Remove all excess spacing around new lines.
                fn (string $html) => preg_replace('/\s*\n\s*/', "\n", $html),

                // Finally, trim the end result.
                fn (string $html) => trim($html),
            ],
            $this->keepNewLines ? [] : [
                // Remove new lines (if configured).
                fn (string $html) => str_replace("\n", ' ', $html),
            ]
        );
    }

    /**
     * Query the HTML for specific element(s) using an XPath expression.
     */
    protected function query(string $html): string
    {
        libxml_use_internal_errors(true);

        $document = new DOMDocument();

        $document->loadHTML($html);

        libxml_use_internal_errors(false);

        $elements = (new DOMXPath($document))->query($this->filter);

        $result = '';

        /** @var \DOMElement $element */
        foreach($elements as $element) {
            $result .= $element->ownerDocument->saveXML($element);
        }

        return $result;
    }

    /**
     * Make a new HTML Purifier instance.
     */
    protected function makePurifier(): HTMLPurifier
    {
        return new HTMLPurifier($this->makePurifierConfig());
    }

    /**
     * Create a new HTML Purifier config.
     */
    protected function makePurifierConfig(): HTMLPurifier_Config
    {
        $allowed = array_filter([
            'p', $this->keepLinks ? 'a[href]' : null,
        ]);

        return HTMLPurifier_Config::create([
            'Cache.SerializerPath' => sys_get_temp_dir(),
            'HTML.Allowed' => implode(',', $allowed),
            'Core.Encoding' => 'utf-8',
            'AutoFormat.RemoveEmpty' => true,
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty.RemoveNbsp' => true,
            'HTML.Doctype' => 'HTML 4.01 Transitional',
        ]);
    }
}
