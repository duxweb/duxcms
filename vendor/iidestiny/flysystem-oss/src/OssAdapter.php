<?php

/*
 * This file is part of the iidestiny/flysystem-oss.
 *
 * (c) iidestiny <iidestiny@vip.qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Iidestiny\Flysystem\Oss;

use Iidestiny\Flysystem\Oss\Traits\SignatureTrait;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\PathPrefixer;
use League\Flysystem\Visibility;
use OSS\Core\OssException;
use OSS\OssClient;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;

/**
 * Class OssAdapter.
 *
 * @author iidestiny <iidestiny@vip.qq.com>
 */
class OssAdapter implements FilesystemAdapter
{
    use SignatureTrait;

    // 系统参数

    const SYSTEM_FIELD = [
        'bucket' => '${bucket}',
        'etag' => '${etag}',
        'filename' => '${object}',
        'size' => '${size}',
        'mimeType' => '${mimeType}',
        'height' => '${imageInfo.height}',
        'width' => '${imageInfo.width}',
        'format' => '${imageInfo.format}',
    ];

    protected $accessKeyId;

    protected $accessKeySecret;

    protected $endpoint;

    protected $bucket;

    protected $isCName;

    /**
     * @var array
     */
    protected $buckets;

    /**
     * @var OssClient
     */
    protected $client;

    /**
     * @var array|mixed[]
     */
    protected $params;

    /**
     * @var bool
     */
    protected $useSSL = false;

    /**
     * @var string|null
     */
    protected $cdnUrl = null;

    /**
     * @var PathPrefixer
     */
    protected $prefixer;

    /**
     * @throws OssException
     */
    public function __construct($accessKeyId, $accessKeySecret, $endpoint, $bucket, bool $isCName = false, string $prefix = '', array $buckets = [], ...$params)
    {
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        $this->endpoint = $endpoint;
        $this->bucket = $bucket;
        $this->isCName = $isCName;
        $this->prefixer = new PathPrefixer($prefix, DIRECTORY_SEPARATOR);
        $this->buckets = $buckets;
        $this->params = $params;
        $this->initClient();
        $this->checkEndpoint();
    }

    /**
     * 设置cdn的url.
     */
    public function setCdnUrl(?string $url)
    {
        $this->cdnUrl = $url;
    }

    public function ossKernel(): OssClient
    {
        return $this->getClient();
    }

    /**
     * 调用不同的桶配置.
     *
     * @return $this
     *
     * @throws OssException
     * @throws \Exception
     */
    public function bucket($bucket): OssAdapter
    {
        if (!isset($this->buckets[$bucket])) {
            throw new \Exception('bucket is not exist.');
        }
        $bucketConfig = $this->buckets[$bucket];

        $this->accessKeyId = $bucketConfig['access_key'];
        $this->accessKeySecret = $bucketConfig['secret_key'];
        $this->endpoint = $bucketConfig['endpoint'];
        $this->bucket = $bucketConfig['bucket'];
        $this->isCName = $bucketConfig['isCName'];

        $this->initClient();
        $this->checkEndpoint();

        return $this;
    }

    /**
     * init oss client.
     *
     * @throws OssException
     */
    protected function initClient()
    {
        $this->client = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint, $this->isCName, ...$this->params);
    }

    /**
     * get ali sdk kernel class.
     */
    public function getClient(): OssClient
    {
        return $this->client;
    }

    /**
     * 验签.
     */
    public function verify(): array
    {
        // oss 前面header、公钥 header
        $authorizationBase64 = '';
        $pubKeyUrlBase64 = '';

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authorizationBase64 = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (isset($_SERVER['HTTP_X_OSS_PUB_KEY_URL'])) {
            $pubKeyUrlBase64 = $_SERVER['HTTP_X_OSS_PUB_KEY_URL'];
        }

        // 验证失败
        if ('' == $authorizationBase64 || '' == $pubKeyUrlBase64) {
            return [false, ['CallbackFailed' => 'authorization or pubKeyUrl is null']];
        }

        // 获取OSS的签名
        $authorization = base64_decode($authorizationBase64);
        // 获取公钥
        $pubKeyUrl = base64_decode($pubKeyUrlBase64);
        // 请求验证
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pubKeyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $pubKey = curl_exec($ch);

        if ('' == $pubKey) {
            return [false, ['CallbackFailed' => 'curl is fail']];
        }

        // 获取回调 body
        $body = file_get_contents('php://input');
        // 拼接待签名字符串
        $path = $_SERVER['REQUEST_URI'];
        $pos = strpos($path, '?');
        if (false === $pos) {
            $authStr = urldecode($path)."\n".$body;
        } else {
            $authStr = urldecode(substr($path, 0, $pos)).substr($path, $pos, strlen($path) - $pos)."\n".$body;
        }
        // 验证签名
        $ok = openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5);

        if (1 !== $ok) {
            return [false, ['CallbackFailed' => 'verify is fail, Illegal data']];
        }

        parse_str($body, $data);

        return [true, $data];
    }

    /**
     * oss 直传配置.
     *
     * @param null $callBackUrl
     *
     * @return false|string
     *
     * @throws \Exception
     */
    public function signatureConfig(string $prefix = '', $callBackUrl = null, array $customData = [], int $expire = 30, int $contentLengthRangeValue = 1048576000, array $systemData = [])
    {
        $prefix = $this->prefixer->prefixPath($prefix);

        // 系统参数
        $system = [];
        if (empty($systemData)) {
            $system = self::SYSTEM_FIELD;
        } else {
            foreach ($systemData as $key => $value) {
                if (!in_array($value, self::SYSTEM_FIELD)) {
                    throw new \InvalidArgumentException("Invalid oss system filed: ${value}");
                }
                $system[$key] = $value;
            }
        }

        // 自定义参数
        $callbackVar = [];
        $data = [];
        if (!empty($customData)) {
            foreach ($customData as $key => $value) {
                $callbackVar['x:'.$key] = $value;
                $data[$key] = '${x:'.$key.'}';
            }
        }

        $callbackParam = [
            'callbackUrl' => $callBackUrl,
            'callbackBody' => urldecode(http_build_query(array_merge($system, $data))),
            'callbackBodyType' => 'application/x-www-form-urlencoded',
        ];
        $callbackString = json_encode($callbackParam);
        $base64CallbackBody = base64_encode($callbackString);

        $now = time();
        $end = $now + $expire;
        $expiration = $this->gmt_iso8601($end);

        // 最大文件大小.用户可以自己设置
        $condition = [
            0 => 'content-length-range',
            1 => 0,
            2 => $contentLengthRangeValue,
        ];
        $conditions[] = $condition;

        $start = [
            0 => 'starts-with',
            1 => '$key',
            2 => $prefix,
        ];
        $conditions[] = $start;

        $arr = [
            'expiration' => $expiration,
            'conditions' => $conditions,
        ];
        $policy = json_encode($arr);
        $base64Policy = base64_encode($policy);
        $stringToSign = $base64Policy;
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->accessKeySecret, true));

        $response = [];
        $response['accessid'] = $this->accessKeyId;
        $response['host'] = $this->normalizeHost();
        $response['policy'] = $base64Policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = $base64CallbackBody;
        $response['callback-var'] = $callbackVar;
        $response['dir'] = $prefix;  // 这个参数是设置用户上传文件时指定的前缀。

        return json_encode($response);
    }

    /**
     * sign url.
     *
     * @return false|\OSS\Http\ResponseCore|string
     */
    public function getTemporaryUrl($path, $timeout, array $options = [], string $method = OssClient::OSS_HTTP_GET)
    {
        $path = $this->prefixer->prefixPath($path);

        try {
            $path = $this->client->signUrl($this->bucket, $path, $timeout, $method, $options);
        } catch (OssException $exception) {
            return false;
        }

        return $path;
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $path = $this->prefixer->prefixPath($path);
        $options = $config->get('options', []);

        try {
            $this->client->putObject($this->bucket, $path, $contents, $options);
        } catch (\Exception $exception) {
            throw UnableToWriteFile::atLocation($path, $exception->getMessage());
        }
    }

    /**
     * Write a new file using a stream.
     */
    public function writeStream(string $path, $contents, Config $config): void
    {
        $path = $this->prefixer->prefixPath($path);
        $options = $config->get('options', []);

        try {
            $this->client->uploadStream($this->bucket, $path, $contents, $options);
        } catch (OssException $exception) {
            throw UnableToWriteFile::atLocation($path, $exception->getErrorCode(), $exception);
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        try {
            $this->copy($source, $destination, $config);
            $this->delete($source);
        } catch (\Exception $exception) {
            throw UnableToMoveFile::fromLocationTo($source, $destination);
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $path = $this->prefixer->prefixPath($source);
        $newPath = $this->prefixer->prefixPath($destination);

        try {
            $this->client->copyObject($this->bucket, $path, $this->bucket, $newPath);
        } catch (OssException $exception) {
            throw UnableToCopyFile::fromLocationTo($source, $destination);
        }
    }

    /**
     * delete a file.
     */
    public function delete(string $path): void
    {
        $path = $this->prefixer->prefixPath($path);

        try {
            $this->client->deleteObject($this->bucket, $path);
        } catch (OssException $ossException) {
            throw UnableToDeleteFile::atLocation($path);
        }
    }

    /**
     * @throws OssException
     */
    public function deleteDirectory(string $path): void
    {
        try {
            $contents = $this->listContents($path, false);
            $files = [];
            foreach ($contents as $i => $content) {
                if ($content instanceof DirectoryAttributes) {
                    $this->deleteDirectory($content->path());
                    continue;
                }
                $files[] = $this->prefixer->prefixPath($content->path());
                if ($i && 0 == $i % 100) {
                    $this->client->deleteObjects($this->bucket, $files);
                    $files = [];
                }
            }
            !empty($files) && $this->client->deleteObjects($this->bucket, $files);
            $this->client->deleteObject($this->bucket, $this->prefixer->prefixDirectoryPath($path));
        } catch (OssException $exception) {
            throw UnableToDeleteDirectory::atLocation($path, $exception->getErrorCode(), $exception);
        }
    }

    public function createDirectory(string $path, Config $config): void
    {
        try {
            $this->client->createObjectDir($this->bucket, $this->prefixer->prefixPath($path));
        } catch (OssException $exception) {
            throw UnableToCreateDirectory::dueToFailure($path, $exception);
        }
    }

    /**
     * visibility.
     *
     * @return array|bool|false
     */
    public function setVisibility(string $path, string $visibility): void
    {
        $object = $this->prefixer->prefixPath($path);

        $acl = Visibility::PUBLIC === $visibility ? OssClient::OSS_ACL_TYPE_PUBLIC_READ : OssClient::OSS_ACL_TYPE_PRIVATE;

        try {
            $this->client->putObjectAcl($this->bucket, $object, $acl);
        } catch (OssException $exception) {
            throw UnableToSetVisibility::atLocation($path, $exception->getMessage());
        }
    }

    public function visibility(string $path): FileAttributes
    {
        try {
            $acl = $this->client->getObjectAcl($this->bucket, $this->prefixer->prefixPath($path), []);
        } catch (OssException $exception) {
            throw UnableToRetrieveMetadata::visibility($path, $exception->getMessage());
        }

        return new FileAttributes($path, null, OssClient::OSS_ACL_TYPE_PRIVATE === $acl ? Visibility::PRIVATE : Visibility::PUBLIC);
    }

    /**
     * Check whether a file exists.
     *
     * @return array|bool|null
     */
    public function fileExists(string $path): bool
    {
        $path = $this->prefixer->prefixPath($path);

        return $this->client->doesObjectExist($this->bucket, $path);
    }

    public function directoryExists(string $path): bool
    {
        return $this->client->doesObjectExist($this->bucket, $this->prefixer->prefixDirectoryPath($path));
    }

    /**
     * Get resource url.
     */
    public function getUrl(string $path): string
    {
        $path = $this->prefixer->prefixPath($path);

        if (!is_null($this->cdnUrl)) {
            return rtrim($this->cdnUrl, '/').'/'.ltrim($path, '/');
        }

        return $this->normalizeHost().ltrim($path, '/');
    }

    /**
     * read a file.
     */
    public function read(string $path): string
    {
        $path = $this->prefixer->prefixPath($path);

        try {
            return $this->client->getObject($this->bucket, $path);
        } catch (\Exception $exception) {
            throw UnableToReadFile::fromLocation($path, $exception->getMessage());
        }
    }

    /**
     * read a file stream.
     *
     * @return array|bool|false
     */
    public function readStream(string $path)
    {
        $stream = fopen('php://temp', 'w+b');

        try {
            fwrite($stream, $this->client->getObject($this->bucket, $path, [OssClient::OSS_FILE_DOWNLOAD => $stream]));
        } catch (OssException $exception) {
            fclose($stream);
            throw UnableToReadFile::fromLocation($path, $exception->getMessage());
        }
        rewind($stream);

        return $stream;
    }

    /**
     * @throws \Exception
     */
    public function listContents(string $path, bool $deep): iterable
    {
        $directory = $this->prefixer->prefixDirectoryPath($path);
        $nextMarker = '';
        while (true) {
            $options = [
                OssClient::OSS_PREFIX => $directory,
                OssClient::OSS_MARKER => $nextMarker,
            ];

            try {
                $listObjectInfo = $this->client->listObjects($this->bucket, $options);
                $nextMarker = $listObjectInfo->getNextMarker();
            } catch (OssException $exception) {
                throw new \Exception($exception->getErrorMessage(), 0, $exception);
            }

            $prefixList = $listObjectInfo->getPrefixList();
            foreach ($prefixList as $prefixInfo) {
                $subPath = $this->prefixer->stripDirectoryPrefix($prefixInfo->getPrefix());
                if ($subPath == $path) {
                    continue;
                }
                yield new DirectoryAttributes($subPath);
                if (true === $deep) {
                    $contents = $this->listContents($subPath, $deep);
                    foreach ($contents as $content) {
                        yield $content;
                    }
                }
            }

            $listObject = $listObjectInfo->getObjectList();
            if (!empty($listObject)) {
                foreach ($listObject as $objectInfo) {
                    $objectPath = $this->prefixer->stripPrefix($objectInfo->getKey());
                    $objectLastModified = strtotime($objectInfo->getLastModified());
                    if ('/' == substr($objectPath, -1, 1)) {
                        continue;
                    }
                    yield new FileAttributes($objectPath, $objectInfo->getSize(), null, $objectLastModified);
                }
            }

            if ('true' !== $listObjectInfo->getIsTruncated()) {
                break;
            }
        }
    }

    public function getMetadata($path): FileAttributes
    {
        try {
            $result = $this->client->getObjectMeta($this->bucket, $this->prefixer->prefixPath($path));
        } catch (OssException $exception) {
            throw UnableToRetrieveMetadata::create($path, 'metadata', $exception->getErrorCode(), $exception);
        }

        $size = isset($result['content-length']) ? intval($result['content-length']) : 0;
        $timestamp = isset($result['last-modified']) ? strtotime($result['last-modified']) : 0;
        $mimetype = $result['content-type'] ?? '';

        return new FileAttributes($path, $size, null, $timestamp, $mimetype);
    }

    /**
     * get the size of file.
     *
     * @return array|false
     */
    public function fileSize(string $path): FileAttributes
    {
        $meta = $this->getMetadata($path);
        if (null === $meta->fileSize()) {
            throw UnableToRetrieveMetadata::fileSize($path);
        }

        return $meta;
    }

    /**
     * get mime type.
     *
     * @return array|false
     */
    public function mimeType(string $path): FileAttributes
    {
        $meta = $this->getMetadata($path);
        if (null === $meta->mimeType()) {
            throw UnableToRetrieveMetadata::mimeType($path);
        }

        return $meta;
    }

    /**
     * get timestamp.
     *
     * @return array|false
     */
    public function lastModified(string $path): FileAttributes
    {
        $meta = $this->getMetadata($path);
        if (null === $meta->lastModified()) {
            throw UnableToRetrieveMetadata::lastModified($path);
        }

        return $meta;
    }

    /**
     * normalize Host.
     */
    protected function normalizeHost(): string
    {
        if ($this->isCName) {
            $domain = $this->endpoint;
        } else {
            $domain = $this->bucket.'.'.$this->endpoint;
        }

        if ($this->useSSL) {
            $domain = "https://{$domain}";
        } else {
            $domain = "http://{$domain}";
        }

        return rtrim($domain, '/').'/';
    }

    /**
     * Check the endpoint to see if SSL can be used.
     */
    protected function checkEndpoint()
    {
        if (0 === strpos($this->endpoint, 'http://')) {
            $this->endpoint = substr($this->endpoint, strlen('http://'));
            $this->useSSL = false;
        } elseif (0 === strpos($this->endpoint, 'https://')) {
            $this->endpoint = substr($this->endpoint, strlen('https://'));
            $this->useSSL = true;
        }
    }
}
