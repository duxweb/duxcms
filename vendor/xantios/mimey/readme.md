Mimey
=====

PHP package for converting file extensions to MIME types and vice versa.

[![Build Status](https://www.travis-ci.com/Xantios/mimey.svg?branch=master)](https://travis-ci.org/xantios/mimey)
[![Maintainability](https://api.codeclimate.com/v1/badges/1f8e322d2c73a16a1830/maintainability)](https://codeclimate.com/github/Xantios/mimey/)
[![Latest Stable Version](https://img.shields.io/packagist/v/xantios/mimey.svg)](https://packagist.org/packages/xantios/mimey)
[![Downloads per Month](https://img.shields.io/packagist/dm/xantios/mimey.svg)](https://packagist.org/packages/xantios/mimey)
[![License](https://img.shields.io/packagist/l/xantios/mimey.svg)](https://packagist.org/packages/ralouphie/mimey)

This package uses [httpd][]'s [mime.types][] to generate a mapping of file extension to MIME type and the other way around.

The `mime.types` file is parsed by `bin/generate.php` and converted into an optimized PHP array in `mime.types.php`
which is then wrapped by helper class `\Mimey\MimeTypes`.

[httpd]: https://httpd.apache.org/docs/current/programs/httpd.html
[mime.types]: https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types

## Usage

```php
$mimes = new \Mimey\MimeTypes;

// Convert extension to MIME type:
$mimes->getMimeType('json'); // application/json

// Convert MIME type to extension:
$mimes->getExtension('application/json'); // json
```

### Getting All

It's rare, but some extensions have multiple MIME types:

```php
// Get all MIME types for an extension:
$mimes->getAllMimeTypes('wmz'); // array('application/x-ms-wmz', 'application/x-msmetafile')
```

However, there are many MIME types that have multiple extensions:

```php
// Get all extensions for a MIME type:
$mimes->getAllExtensions('image/jpeg'); // array('jpeg', 'jpg', 'jpe')
```

### Custom Conversions

You can add custom conversions by changing the mapping that is given to `MimeTypes`.

There is a `MimeMappingBuilder` that can help with this:

```php
// Create a builder using the built-in conversions as the basis.
$builder = \Mimey\MimeMappingBuilder::create();

// Add a conversion. This conversion will take precedence over existing ones.
$builder->add('custom/mime-type', 'myextension');

$mimes = new \Mimey\MimeTypes($builder->getMapping());
$mimes->getMimeType('myextension'); // custom/mime-type
$mimes->getExtension('custom/mime-type'); // myextension
```

You can add as many conversions as you would like to the builder:

```php
$builder->add('custom/mime-type', 'myextension');
$builder->add('foo/bar', 'foobar');
$builder->add('foo/bar', 'fbar');
$builder->add('baz/qux', 'qux');
$builder->add('cat/qux', 'qux');
...
```

#### Optimized Custom Conversion Loading

You can optimize the loading of custom conversions by saving all conversions to a compiled PHP file as part of a build step.

```php
// Add a bunch of custom conversions.
$builder->add(...);
$builder->add(...);
$builder->add(...);
...
// Save the conversions to a cached file.
$builder->save($cache_file_path);
```

The file can then be loaded to avoid overhead of repeated `$builder->add(...)` calls:

```php
// Load the conversions from a cached file.
$builder = \Mimey\MimeMappingBuilder::load($cache_file_path);
$mimes = new \Mimey\MimeTypes($builder->getMapping());
```

#### Programmatically or manually add custom type mappings

You can rename the included `mime.types.custom.example` to `mime.types.custom` and recompile. the custom entries always take precedence over built-in defitions.

#### Updating (advanced)

In case there is a update in the httpd defined types you can run `bin/pull.php` in this repo to pull new mime files and compile them

## Install

Compatible with PHP >= 5.4.

```
composer require xantios/mimey
```

## Credits

Original version created by rodolfoberrios
