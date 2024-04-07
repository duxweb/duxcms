<?php

use Stevebauman\Hypertext\Transformer;

function fixture(string $path): string
{
    return __DIR__ . "/../Fixtures/$path";
}

function transformer(): Transformer {
    return new Transformer();
}

it('trims unnecessary spacing', function () {
    expect(
        transformer()->toText('   foo   bar   ')
    )->toEqual('foo bar');
});

it('trims new lines', function () {
    expect(
        transformer()->toText("   foo \n  bar   ")
    )->toEqual('foo bar');
});

it('captures text', function () {
    expect(
        transformer()->toText('foo bar')
    )->toEqual('foo bar');
});

it('captures text with new lines when enabled', function () {
    expect(
        transformer()->keepNewLines()->toText("foo \n bar")
    )->toEqual("foo\nbar");
});

it('captures text within links', function () {
    expect(
        transformer()->toText('<a href="localhost">Some Link</a>')
    )->toEqual("Some Link");
});

it('captures text with links when enabled', function () {
    expect(
        transformer()->keepLinks()->toText('<a href="localhost"> Some Link </a>')
    )->toEqual('<a href="localhost"> Some Link </a>');
});

it('adds space around html elements', function () {
    expect(
        transformer()->toText(<<<HTML
        <div>foo</div><a>bar</a><p>baz</p>
        HTML)
    )->toEqual('foo bar baz');
});

it('captures text within html', function (string $inputFile, string $outputFile, Closure $callback = null) {
    $input = file_get_contents(fixture($inputFile));
    $output = file_get_contents(fixture($outputFile));

    $transformer = transformer();

    if ($callback) {
        $callback($transformer);
    }

    expect($transformer->toText($input))->toEqual($output);
})->with([
    [
        'email/input.txt',
        'email/output.txt',
    ],
    [
        'email/input.txt',
        'email/output-links.txt',
        fn (Transformer $transformer) => $transformer->keepLinks(),
    ],
    [
        'email/input.txt',
        'email/output-lines.txt',
        fn (Transformer $transformer) => $transformer->keepNewLines(),
    ],
    [
        'email/input.txt',
        'email/output-both.txt',
        fn (Transformer $transformer) => $transformer->keepLinks()->keepNewLines(),
    ],

    [
        'laravel.com/input.txt',
        'laravel.com/output.txt',
    ],
    [
        'laravel.com/input.txt',
        'laravel.com/output-links.txt',
        fn (Transformer $transformer) => $transformer->keepLinks(),
    ],
    [
        'laravel.com/input.txt',
        'laravel.com/output-lines.txt',
        fn (Transformer $transformer) => $transformer->keepNewLines(),
    ],
    [
        'laravel.com/input.txt',
        'laravel.com/output-both.txt',
        fn (Transformer $transformer) => $transformer->keepLinks()->keepNewLines(),
    ],
    [
        'html2text/huge-msoffice/input.txt',
        'html2text/huge-msoffice/output.txt',
    ],
    [
        'html2text/huge-msoffice/input.txt',
        'html2text/huge-msoffice/output-lines.txt',
        fn (Transformer $transformer) => $transformer->keepNewLines(),
    ],
]);

it('it captures text only within filter selector', function () {
    expect(
        transformer()->filter('//a')->toText(<<<HTML
        <div>foo</div><a>bar</a><p>baz</p>
        HTML)
    )->toEqual('bar');
});

it('it captures text only within filter selector with larger input', function () {
    $input = file_get_contents(fixture('laravel.com/input.txt'));
    $output = file_get_contents(fixture('laravel.com/output-filter-xpath.txt'));

    expect(transformer()->filter('//footer')->keepLinks()->keepNewLines()->toText($input))->toEqual($output);
});
