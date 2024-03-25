<?php

namespace Overtrue\CosClient;

use Overtrue\CosClient\Exceptions\ClientException;
use Overtrue\CosClient\Exceptions\Exception;
use Overtrue\CosClient\Exceptions\InvalidConfigException;
use Overtrue\CosClient\Exceptions\ServerException;
use Overtrue\CosClient\Http\Response;
use Overtrue\CosClient\Middleware\CreateRequestSignature;
use Overtrue\CosClient\Middleware\SetContentMd5;
use Overtrue\CosClient\Traits\CreatesHttpClient;

/**
 * @method \Overtrue\CosClient\Http\Response get($uri, array $options = [])
 * @method \Overtrue\CosClient\Http\Response head($uri, array $options = [])
 * @method \Overtrue\CosClient\Http\Response options($uri, array $options = [])
 * @method \Overtrue\CosClient\Http\Response put($uri, array $options = [])
 * @method \Overtrue\CosClient\Http\Response post($uri, array $options = [])
 * @method \Overtrue\CosClient\Http\Response patch($uri, array $options = [])
 * @method \Overtrue\CosClient\Http\Response delete($uri, array $options = [])
 * @method \Overtrue\CosClient\Http\Response request(string $method, $uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface getAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface headAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface optionsAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface putAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface postAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface patchAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface deleteAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface requestAsync(string $method, $uri, array $options = [])
 */
class Client
{
    use CreatesHttpClient;

    public const DEFAULT_REGION = 'ap-guangzhou';

    protected Config $config;

    protected string $domain = '<bucket>-<app_id>.cos.<region>.myqcloud.com';

    protected \GuzzleHttp\Client $client;

    protected array $requiredConfigKeys = [];

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function __construct(array|Config $config)
    {
        $this->config = $this->normalizeConfig($config);

        $this->configureDomain();
        $this->configureMiddlewares();
        $this->configureHttpClientOptions();
    }

    public function getSchema(): string
    {
        return $this->config->get('use_https', true) ? 'https' : 'http';
    }

    public function getAppId(): int
    {
        return $this->config->get('app_id', 0);
    }

    public function getSecretId(): string
    {
        return $this->config->get('secret_id', '');
    }

    public function getSecretKey(): string
    {
        return $this->config->get('secret_key', '');
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getHttpClient(): \GuzzleHttp\Client
    {
        return $this->client ?? $this->client = $this->createHttpClient();
    }

    /**
     * @throws ServerException
     * @throws Exception
     * @throws ClientException
     */
    public function __call($method, $arguments)
    {
        try {
            return new Response(\call_user_func_array([$this->getHttpClient(), $method], $arguments));
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new ClientException($e);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            throw new ServerException($e);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    protected function configureDomain(): static
    {
        $replacements = [
            '<uin>' => $this->config->get('uin'),
            '<app_id>' => $this->config->get('app_id'),
            '<region>' => $this->config->get('region') ?? self::DEFAULT_REGION,
            '<bucket>' => $this->config->get('bucket'),
        ];

        $domain = $this->config->get('domain');

        $this->domain = trim($domain ?: str_replace(array_keys($replacements), $replacements, $this->domain), '/');

        return $this;
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function normalizeConfig(array|Config $config): Config
    {
        if (is_array($config)) {
            $config = new Config($config);
        }

        $requiredKeys = ['app_id', 'secret_id', 'secret_key', ...$this->requiredConfigKeys];

        foreach ($requiredKeys as $key) {
            if ($config->missing($key)) {
                throw new InvalidConfigException(sprintf('%s was required.', implode(', ', $requiredKeys)));
            }
        }

        return $config;
    }

    public function configureMiddlewares(): void
    {
        $this->pushMiddleware(
            new CreateRequestSignature(
                $this->getSecretId(),
                $this->getSecretKey(),
                $this->config->get('signature_expires')
            )
        );

        $this->pushMiddleware(new SetContentMd5());
    }

    public static function spy(): Client|\Mockery\MockInterface|\Mockery\LegacyMockInterface
    {
        return \Mockery::mock(static::class);
    }

    public static function partialMock(): \Mockery\MockInterface
    {
        $mock = \Mockery::mock(static::class)->makePartial();
        $mock->shouldReceive('getHttpClient')->andReturn(\Mockery::mock(\GuzzleHttp\Client::class));

        return $mock;
    }

    public static function partialMockWithConfig(array|Config $config, array $methods = ['get', 'post', 'patch', 'put', 'delete']): \Mockery\MockInterface
    {
        if (\is_array($config)) {
            $config = new Config($config);
        }

        $mock = \Mockery::mock(static::class.\sprintf('[%s]', \implode(',', $methods)), [$config]);
        $mock->shouldReceive('getHttpClient')->andReturn(\Mockery::mock(\GuzzleHttp\Client::class));

        return $mock;
    }

    protected function configureHttpClientOptions(): void
    {
        $this->setBaseUri(\sprintf('%s://%s/', $this->getSchema(), $this->domain));
        $this->mergeHttpClientOptions($this->config->get('guzzle', [
            'headers' => [
                'User-Agent' => 'overtrue/qcloud-cos-client:'.\GuzzleHttp\Client::MAJOR_VERSION,
            ],
        ]));
    }
}
