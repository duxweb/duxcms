<?php

namespace App\Tools\Service;

use App\Tools\Models\ToolsPoster;
use Dux\Handlers\ExceptionBusiness;
use Intervention\Image\Geometry\Factories\CircleFactory;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class Poster
{
    public static function generate(int $id, $params = []): void
    {
        $info = ToolsPoster::query()->find($id);
        if (!$info) {
            throw new ExceptionBusiness('海报模板不存在');
        }
        $data = $info->data;
        $manager = new ImageManager(Driver::class);
        $canvas = $manager->create($data['config']['width'], $data['config']['height'])->fill($data['config']['color']);

        // 插入背景图
        if ($data['config']['image']) {
            $image = $manager->read($data['config']['image']);
            $image->resize($data['config']['width'], $data['config']['height']);
            $canvas->place($image);
        }

        // 插入图层
        foreach ($data['data']['objects'] as $object) {
            switch ($object['type']) {
                case 'circle':

                    if ($object['label'] && $params[$object['label']]) {
                        $image = $manager->read($params[$object['label']]);
                        $image->cover($object['width'], $object['height']);

                        $mask = $manager->create($object['width'], $object['width']);
                        $mask->drawCircle($object['width']/2, $object['width']/2, function (CircleFactory $circle) use ($object) {
                            $circle->size($object['width'], $object['height']);
                            $circle->background($object['fill']);
                        });
                        $image->mask($mask->encode('png', 75), true);

                        $canvas->place($image, 'top-left', $object['left'], $object['top']);
                    }else {

                        $canvas->drawCircle($object['left'], $object['top'], function (CircleFactory $circle) use ($object) {
                            $circle->size($object['width'], $object['height']);
                            $circle->background($object['fill']);
                        });
                    }

                    break;
                case 'rect':
                    $canvas->drawRectangle($object['left'], $object['top'], function (RectangleFactory $draw) use ($object) {
                        $draw->size($object['width'], $object['height']);
                        $draw->background($object['fill']);
                    });
                    break;
                case 'textbox':
                    $canvas->text($object['text'], $object['left'], $object['top'], function ($font) use ($object) {
                        $font->file(public_path('path_to_your_font.ttf')); // Change path to your font file
                        $font->size($object['fontSize']);
                        $font->align($object['textAlign']);
                        $font->valign('top');
                    });
                    break;
            }
        }


    }

}