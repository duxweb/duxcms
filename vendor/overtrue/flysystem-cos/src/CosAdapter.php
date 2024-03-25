<?php

namespace Overtrue\Flysystem\Cos;

use GuzzleHttp\Psr7\Uri;
use JetBrains\PhpStorm\Pure;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathPrefixer;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;
use Overtrue\CosClient\BucketClient;
use Overtrue\CosClient\Exceptions\ClientException;
use Overtrue\CosClient\ObjectClient;
use TheNorthMemory\Xml\Transformer;

class CosAdapter implements FilesystemAdapter
{
    protected ?ObjectClient $objectClient;

    protected ?BucketClient $bucketClient;

    protected PathPrefixer $prefixer;

    protected array $config;

    #[Pure]
    public function __construct(array $config)
    {
        $this->config = \array_merge(
            [
                'bucket' => null,
                'app_id' => null,
                'region' => 'ap-guangzhou',
                'signed_url' => false,
            ],
            $config
        );

        $this->prefixer = new PathPrefixer($config['prefix'] ?? '', DIRECTORY_SEPARATOR);
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     * @throws \Throwable
     */
    public function fileExists(string $path): bool
    {
        return $this->getMetadata($path) !== false;
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function directoryExists(string $path): bool
    {
        return $this->fileExists($path);
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function write(string $path, string $contents, Config $config): void
    {
        $prefixedPath = $this->prefixer->prefixPath($path);
        $response = $this->getObjectClient()->putObject($prefixedPath, $contents, $config->get('headers', []));

        if (! $response->isSuccessful()) {
            throw UnableToWriteFile::atLocation($path, (string) $response->getBody());
        }

        if ($visibility = $config->get('visibility')) {
            $this->setVisibility($path, $visibility);
        }
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->write($path, \stream_get_contents($contents), $config);
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function read(string $path): string
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        $response = $this->getObjectClient()->getObject($prefixedPath);
        if ($response->isNotFound()) {
            throw UnableToReadFile::fromLocation($path, (string) $response->getBody());
        }

        return (string) $response->getBody();
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function readStream(string $path)
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        $response = $this->getObjectClient()->get(\urlencode($prefixedPath), ['stream' => true]);

        if ($response->isNotFound()) {
            return false;
        }

        return $response->getBody()->detach();
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function delete(string $path): void
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        $response = $this->getObjectClient()->deleteObject($prefixedPath);

        if (! $response->isSuccessful()) {
            throw UnableToDeleteFile::atLocation($path, (string) $response->getBody());
        }
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function deleteDirectory(string $path): void
    {
        $dirname = $this->prefixer->prefixPath($path);

        $response = $this->listObjects($dirname);

        if (empty($response['Contents'])) {
            return;
        }

        $keys = array_map(
            function ($item) {
                return ['Key' => $item['Key']];
            },
            $response['Contents']
        );

        $response = $this->getObjectClient()->deleteObjects(
            [
                'Quiet' => 'false',
                'Object' => Transformer::wrap($keys, true, 'Object'),
            ]
        );

        if (! $response->isSuccessful()) {
            throw UnableToDeleteDirectory::atLocation($path, (string) $response->getBody());
        }
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function createDirectory(string $path, Config $config): void
    {
        $dirname = $this->prefixer->prefixPath($path);

        $this->getObjectClient()->putObject($dirname.'/', '');
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function setVisibility(string $path, string $visibility): void
    {
        $this->getObjectClient()->putObjectACL(
            $this->prefixer->prefixPath($path),
            [],
            [
                'x-cos-acl' => $this->normalizeVisibility($visibility),
            ]
        );
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function visibility(string $path): FileAttributes
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        $meta = $this->getObjectClient()->getObjectACL($prefixedPath);

        foreach ($meta['AccessControlPolicy']['AccessControlList']['Grant'] ?? [] as $grant) {
            if ($grant['Permission'] === 'READ' && str_contains($grant['Grantee']['URI'] ?? '', 'global/AllUsers')) {
                return new FileAttributes($path, null, Visibility::PUBLIC);
            }
        }

        return new FileAttributes($path, null, Visibility::PRIVATE);
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     * @throws \Throwable
     */
    public function mimeType(string $path): FileAttributes
    {
        $meta = $this->getMetadata($path);
        if (! $meta || $meta->mimeType() === null) {
            throw UnableToRetrieveMetadata::mimeType($path);
        }

        return $meta;
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     * @throws \Throwable
     */
    public function lastModified(string $path): FileAttributes
    {
        $meta = $this->getMetadata($path);

        if (! $meta || $meta->lastModified() === null) {
            throw UnableToRetrieveMetadata::lastModified($path);
        }

        return $meta;
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     * @throws \Throwable
     */
    public function fileSize(string $path): FileAttributes
    {
        $meta = $this->getMetadata($path);

        if (! $meta || $meta->fileSize() === null) {
            throw UnableToRetrieveMetadata::fileSize($path);
        }

        return $meta;
    }

    public function listContents(string $path, bool $deep): iterable
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        $response = $this->listObjects($prefixedPath, $deep);

        // 处理目录
        foreach ($response['CommonPrefixes'] ?? [] as $prefix) {
            yield new DirectoryAttributes($prefix['Prefix']);
        }

        foreach ($response['Contents'] ?? [] as $content) {
            yield new FileAttributes(
                $content['Key'],
                \intval($content['Size']),
                null,
                \strtotime($content['LastModified'])
            );
        }
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidArgumentException
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function move(string $source, string $destination, Config $config): void
    {
        $this->copy($source, $destination, $config);

        $this->delete($this->prefixer->prefixPath($source));
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidArgumentException
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function copy(string $source, string $destination, Config $config): void
    {
        $prefixedSource = $this->prefixer->prefixPath($source);

        $location = $this->getSourcePath($prefixedSource);

        $prefixedDestination = $this->prefixer->prefixPath($destination);

        $response = $this->getObjectClient()->copyObject(
            $prefixedDestination,
            [
                'x-cos-copy-source' => $location,
            ]
        );
        if (! $response->isSuccessful()) {
            throw UnableToCopyFile::fromLocationTo($source, $destination);
        }
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function getUrl(string $path): string
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        if (! empty($this->config['cdn'])) {
            return \strval(new Uri(\sprintf('%s/%s', \rtrim($this->config['cdn'], '/'), $prefixedPath)));
        }

        return $this->config['signed_url'] ? $this->getSignedUrl($path) : $this->getObjectClient()->getObjectUrl($prefixedPath);
    }

    /**
     * For laravel FilesystemAdapter.
     *
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function getTemporaryUrl($path, int|string|\DateTimeInterface $expiration): string
    {
        if ($expiration instanceof \DateTimeInterface) {
            $expiration = $expiration->getTimestamp();
        }

        return $this->getSignedUrl($path, $expiration);
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function getSignedUrl(string $path, int|string $expires = '+60 minutes'): string
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        return $this->getObjectClient()->getObjectSignedUrl($prefixedPath, $expires);
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function getObjectClient(): ObjectClient
    {
        return $this->objectClient ?? $this->objectClient = new ObjectClient($this->config);
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function getBucketClient(): BucketClient
    {
        return $this->bucketClient ?? $this->bucketClient = new BucketClient($this->config);
    }

    public function setObjectClient(ObjectClient $objectClient): CosAdapter
    {
        $this->objectClient = $objectClient;

        return $this;
    }

    public function setBucketClient(BucketClient $bucketClient): CosAdapter
    {
        $this->bucketClient = $bucketClient;

        return $this;
    }

    protected function getSourcePath(string $path): string
    {
        return sprintf(
            '%s-%s.cos.%s.myqcloud.com/%s',
            $this->config['bucket'],
            $this->config['app_id'],
            $this->config['region'],
            $path
        );
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     * @throws \Throwable
     */
    protected function getMetadata($path): bool|FileAttributes
    {
        try {
            $prefixedPath = $this->prefixer->prefixPath($path);

            $meta = $this->getObjectClient()->headObject($prefixedPath)->getHeaders();
            if (empty($meta)) {
                return false;
            }
        } catch (\Throwable $e) {
            if ($e instanceof ClientException && $e->getCode() === 404) {
                return false;
            }

            throw $e;
        }

        return new FileAttributes(
            $path,
            isset($meta['Content-Length'][0]) ? \intval($meta['Content-Length'][0]) : null,
            null,
            isset($meta['Last-Modified'][0]) ? \strtotime($meta['Last-Modified'][0]) : null,
            $meta['Content-Type'][0] ?? null,
        );
    }

    protected function listObjects(string $directory = '', bool $recursive = false)
    {
        $result = $this->getBucketClient()->getObjects(
            [
                'prefix' => empty($directory) ? '' : ($directory.'/'),
                'delimiter' => $recursive ? '' : '/',
            ]
        )->toArray();

        foreach (['CommonPrefixes', 'Contents'] as $key) {
            $result[$key] = $result[$key] ?? [];

            // 确保是二维数组
            if (($index = \key($result[$key])) !== 0) {
                $result[$key] = \is_null($index) ? [] : [$result[$key]];
            }

            //过滤掉目录
            if ($key === 'Contents') {
                $result[$key] = \array_filter($result[$key], function ($item) {
                    return ! \str_ends_with($item['Key'], '/');
                });
            }
        }

        return $result;
    }

    protected function normalizeVisibility(string $visibility): string
    {
        return match ($visibility) {
            Visibility::PUBLIC => 'public-read',
            Visibility::PRIVATE => 'private',
            default => 'default',
        };
    }
}
