<?php

namespace Stevebauman\Hypertext;

use Closure;
use HTMLPurifier;
use HTMLPurifier_Config;

class Transformer
{
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
        return array_merge([
            // Convert any quoted-printable strings to an 8 bit string.
            fn (string $text) => quoted_printable_decode($text),

            // Add spacing between HTML tags.
            fn (string $text) => preg_replace('/(>)(<)/', '$1 $2', $text),

            // Remove various forms of unneeded spaces.
            fn (string $text) => str_replace($this->spaces, ' ', $text),

            // Strip all CSS, HTML tags, and scripts.
            fn (string $text) => $this->makePurifier()->purify($text),

            // Strip all remaining HTML tags after purification.
            fn (string $text) => strip_tags($text, $this->keepLinks ? '<a>' : null),

            // Remove all horizontal spaces.
            fn (string $text) => preg_replace( '/\h+/u', ' ', $text),

            // Remove all excess spacing around new lines.
            fn (string $text) => preg_replace('/\s*\n\s*/', "\n", $text),

            // Finally, trim the end result.
            fn (string $text) => trim($text),
        ], $this->keepNewLines ? [] : [
            // Remove new lines (if configured).
            fn (string $text) => str_replace("\n", ' ', $text),
        ]);
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
