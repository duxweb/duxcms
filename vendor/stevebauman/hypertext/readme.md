<h1 align="center">Hypertext</h1>

<p align="center">
A PHP HTML to pure text transformer that beautifully handles various and malformed HTML.
</p>

<p align="center">
<a href="https://github.com/stevebauman/hypertext/actions" target="_blank"><img src="https://img.shields.io/github/actions/workflow/status/stevebauman/hypertext/run-tests.yml?branch=master&style=flat-square"/></a>
<a href="https://packagist.org/packages/stevebauman/hypertext" target="_blank"><img src="https://img.shields.io/packagist/v/stevebauman/hypertext.svg?style=flat-square"/></a>
<a href="https://packagist.org/packages/stevebauman/hypertext" target="_blank"><img src="https://img.shields.io/packagist/dt/stevebauman/hypertext.svg?style=flat-square"/></a>
<a href="https://packagist.org/packages/stevebauman/hypertext" target="_blank"><img src="https://img.shields.io/packagist/l/stevebauman/hypertext.svg?style=flat-square"/></a>
</p>

---

Hypertext is excellent at pulling text content out of any HTML based document and automatically:

- Removes CSS
- Removes scripts
- Removes headers
- Removes non-HTML based content
- Preserves spacing 
- Preserves links (optional)
- Preserves new lines (optional)

It is directed at using the output in LLM related tasks, such as prompts and embeddings.

## Installation

```bash
composer require stevebauman/hypertext
```

## Usage

```php
use Stevebauman\Hypertext\Transformer;

$transformer = new Transformer();

// (Optional) Filter out specific elements by their XPath.
$transformer->filter("//*[@id='some-element']");

// (Optional) Retain new line characters.
$transformer->keepNewLines();

// (Optional) Retain anchor tags and their href attribute.
$transformer->keepLinks();

$text = $transformer->toText($html);
```

## Example

> For larger examples, please view the [tests/Fixtures](https://github.com/stevebauman/hypertext/tree/master/tests/Fixtures) directory.

**Input**:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog</title>
</head>
<body>
    <h1>Welcome to My Blog</h1>
    <p>This is a paragraph of text on my webpage.</p>
    <a href="https://blog.com/posts">Click here</a> to view my posts.
</body>
</html>
```

**Output (Pure Text)**:

```php
echo (new Transformer)->toText($html);
```

```text
Welcome to My Blog This is a paragraph of text on my webpage. Click here to view my posts.
```

**Output (Keep New Lines)**:

```php
echo (new Transformer)->keepNewLines()->toText($html);
```

```text
Welcome to My Blog
This is a paragraph of text on my webpage.
Click here to view my posts.
```

**Output (Keep Links)**:

```php
echo (new Transformer)->keepLinks()->toText($html);
```

```text
Welcome to My Blog This is a paragraph of text on my webpage. <a href="https://blog.com/posts">Click Here</a> to view my posts.
```

**Output (Keep Both)**:

```php
echo (new Transformer)
    ->keepLinks()
    ->keepNewLines()
    ->toText($html);
```

```text
Welcome to My Blog
This is a paragraph of text on my webpage.
<a href="https://blog.com/posts">Click Here</a> to view my posts.
```
