<p align="center">
  <a href="https://github.com/BenjaminHoegh/ParsedownToc">
    <img alt="ParsedownToc" src="https://github.com/BenjaminHoegh/ParsedownToc/blob/master/.github/parsedownToc.png" height="330" />
  </a>
</p>

# ParsedownToc
![GitHub release](https://img.shields.io/github/release/BenjaminHoegh/ParsedownToc.svg?style=flat-square)
![GitHub](https://img.shields.io/github/license/BenjaminHoegh/ParsedownToc.svg?style=flat-square)

**ParsedownToc** is an extension for Parsedown and ParsedownExtra that introduces advanced features for developers working with Markdown. It is based on [@KEINOS toc extention](https://github.com/KEINOS/parsedown-extension_table-of-contents)

> [!NOTE]
> Does not yet include the lasted changes in ParsedownExtended v1.2.0

## Features:
- **Speed:** Super-fast processing.
- **Configurability:** Easily customizable for different use-cases.
- **Custom Header IDs:** Full support for custom header ids.

## Prerequisites:
- Requires Parsedown 1.7.4 or later.

## Installation:
1. Use Composer to install the [ParsedownToc package from packagist.org](https://packagist.org/packages/hoegh/ParsedownToc):
   ```bash
   composer require benjaminhoegh/ParsedownToc
   ```
2. Alternatively, download the [latest release](https://github.com/BenjaminHoegh/ParsedownToc/releases/latest) and include `Parsedown.php`.

## Usage:
**Basic example:**
```php
<?php
require 'vendor/autoload.php';  // autoload

$content = file_get_contents('sample.md');  // Sample Markdown with '[toc]' tag
$ParsedownToc = new ParsedownToc();

$html = $ParsedownToc->text($content);  // Parses '[toc]' tag to ToC if exists
echo $html;
```

**Separate body and ToC:**
```php
<?php
$content = file_get_contents('sample.md');
$ParsedownToc = new \ParsedownToc();

$body = $ParsedownToc->body($content);
$toc  = $ParsedownToc->contentsList();

echo $toc;  // ToC in <ul> list
echo $body; // Main content
```

## Configuration:
Use the `ParsedownToc->setOptions(array $options)` method to configure the main class. The available options include:

| Option         | Type     | Default                                 | Description                                                   |
|----------------|----------|-----------------------------------------|---------------------------------------------------------------|
| selectors      | array    | ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']    |                                                               |
| delimiter      | string   | `-`                                     |                                                               |
| limit          | int      | `null`                                  |                                                               |
| lowercase      | boolean  | `true`                                  |                                                               |
| replacements   | array    | none                                    |                                                               |
| transliterate  | boolean  | `false`                                 |                                                               |
| urlencode      | boolean  | `false`                                 | Uses PHP built-in `urlencode` and disables all other options. |
| url            | string   | ``                                      | Prefixes anchor with the specified URL.                       |

### Methods:
The ParsedownToc class offers several methods for different functionalities:

- **text(string $text):** Returns the parsed content and `[toc]` tag(s).
- **body(string $text):** Returns the parsed content without the `[toc]` tag.
- **contentsList([string $type_return='html']):** Returns the ToC in HTML, JSON, or as an array.
    - _Optional:_ Specify the return type as `html`, `json`, or `array`.
- **setTocSelectors(array $array):** Allows you to set specific selectors.
- **setTocDelimiter(string $delimiter):** Define a custom delimiter.
- **setTocLimit(int $limit):** Set a limit for the table of contents.
- **setTocLowercase(bool $boolean):** Choose whether the output should be in lowercase.
- **setTocReplacements(array $replacements):** Provide replacements for specific content.
- **setTocTransliterate(bool $boolean):** Specify if transliterations should be made.
- **setTocUrlencode(bool $boolean):** Decide if you want to use PHP's built-in `urlencode`.
- **setTocBlacklist(array $blacklist):** Blacklist specific IDs from header anchor generation.
- **setTocUrl(string $url):** Set a specific URL prefix for anchors.
- **setTocTag(string $tag='[tag]'):** Set a custom ToC markdown tag.
- **setTocId(string $id):** Set a custom ID for the table of contents.
