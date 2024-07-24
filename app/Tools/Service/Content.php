<?php

namespace App\Tools\Service;

use Pelago\Emogrifier\CssInliner;

class Content
{
    public static function fromHtml(?string $content = null): string
    {
        $css = file_get_contents(__DIR__ . '/../Static/content.css');
        return CssInliner::fromHtml('<div class="typo">'.$content.'</div>')->inlineCss($css)->renderBodyContent();
    }

}