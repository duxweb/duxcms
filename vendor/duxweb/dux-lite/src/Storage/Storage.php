<?php
declare(strict_types=1);

namespace Dux\Storage;

use Dux\App;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Overtrue\Flysystem\Cos\CosAdapter;
use Overtrue\Flysystem\Qiniu\QiniuAdapter;
use Iidestiny\Flysystem\Oss\OssAdapter;

class Storage {
    public static function init(string $type, array $config): Filesystem {
        switch ($type) {
            case "qiniu":
                // https://github.com/overtrue/flysystem-qiniu
                $adapter = new QiniuAdapter($config["accessKey"], $config["secretKey"], $config["bucket"], $config["domain"]);
                break;
            case "cos":
                // https://github.com/overtrue/flysystem-cos
                $adapter = new CosAdapter($config);
                break;
            case "oss":
                $adapter = new OssAdapter($config["accessKeyId"], $config["accessKeySecret"], $config["endpoint"], $config["bucket"], $config["isCName"], $config["prefix"] ?: '');
                $adapter->setCdnUrl($config["domain"]);
                break;
            default:
                $adapter = new LocalFilesystemAdapter(
                    App::$basePath . "/" . $config["path"]
                );
        }
        return new Filesystem(
            $adapter,
            ["public_url" => $config["public_url"]]
        );

    }
}