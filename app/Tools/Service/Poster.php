<?php

namespace App\Tools\Service;

use App\Tools\Models\ToolsPoster;
use Dux\Handlers\ExceptionBusiness;
use Intervention\Image\Geometry\Factories\CircleFactory;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Typography\FontFactory;

class Poster
{
    public static function generate(int $id, $params = [], string $fontPath = ''): string
    {

        $fontPath = $fontPath ?: config_path('font/AlibabaPuHuiTi-3-55-Regular.ttf');

        if (!$fontPath || !is_file($fontPath)) {
            throw new ExceptionBusiness('Font file does not exist');
        }
        $info = ToolsPoster::query()->find($id);
        if (!$info) {
            throw new ExceptionBusiness('Template does not exist');
        }
        $data = $info->data;
        $manager = new ImageManager(Driver::class);
        $canvas = $manager->create($data['config']['width'], $data['config']['height'])->fill($data['config']['color']);

        // 插入背景图
        if ($data['config']['image']) {
            $image = $manager->read(file_get_contents($data['config']['image']));
            $image->resize($data['config']['width'], $data['config']['height']);
            $canvas->place($image);
        }

        // 插入图层
        foreach ($data['data']['objects'] as $object) {

            $width = $object['width'] * ($object['scaleX'] ?: 1);
            $height = $object['height'] * ($object['scaleY'] ?: 1);

            switch ($object['type']) {
                case 'circle':
                    if ($object['label'] && $params[$object['label']]) {
                        $image = $manager->read(file_get_contents($params[$object['label']]));
                        $image->cover($width, $height);
                        $image->core()->native()->roundCorners($width / 2, $height / 2);
                        $canvas->place($image, 'top-left', $object['left'], $object['top']);
                    } else {
                        $canvas->drawCircle($object['left'] + $width / 2, $object['top'] + $height / 2, function (CircleFactory $circle) use ($object, $width, $height) {
                            $circle->size($width, $height);
                            $circle->background($object['fill']);
                        });
                    }
                    break;
                case 'rect':
                    if ($object['label'] && $params[$object['label']]) {
                        $image = $manager->read(file_get_contents($params[$object['label']]));
                        $image->cover($width, $height);
                        $canvas->place($image, 'top-left', $object['left'], $object['top']);
                    } else {
                        $canvas->drawRectangle($object['left'], $object['top'], function (RectangleFactory $draw) use ($object, $width, $height) {
                            $draw->size($width, $height);
                            $draw->background($object['fill']);
                        });
                    }
                    break;
                case 'textbox':
                    $left = $object['angle'] > 0 ? $object['left'] : $object['left'] + $width / 2;
                    $top = $object['angle'] > 0 ? $object['top'] : $object['top'] + $height / 2;
                    $text = $object['text'];
                    foreach ($params as $key => $vo) {
                        $text = str_replace('{' . $key . '}', $vo, $text);
                    }
                    $canvas->text($text, $left, $top, function (FontFactory $font) use ($object, $width, $height, $fontPath) {
                        $font->file($fontPath);
                        $font->wrap($width);
                        $font->size($object['fontSize']);
                        $font->align($object['textAlign'] ?: 'left');
                        $font->lineHeight(1.8);
                        $font->angle($object['angle'] ?: 0);
                        $font->valign('top');
                    });
                    break;
                case 'image':
                    $image = $manager->read(file_get_contents($object['src']));
                    $image->resize($width, $height);

                    $angle = $object['angle'] ? ($object['angle'] <= 180 ? -$object['angle'] : 360 - $object['angle']) : 0;
                    $rotateImage = $image->rotate($angle, 'transparent');
                    $left = $object['left'];
                    $top = $object['top'];

                    if ($object['angle']) {
                        $left = $object['angle'] <= 180 ? $left - $rotateImage->width() / 2 : $left;
                        $top = $object['angle'] > 180 ? $top - $rotateImage->height() / 2 : $top;
                    }
                    $canvas->place($image, 'top-left', $left, $top);
                    break;
            }
        }

        return $canvas->toJpeg(quality: 80)->toString();
    }

}