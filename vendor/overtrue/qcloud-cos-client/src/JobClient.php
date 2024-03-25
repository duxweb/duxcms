<?php

namespace Overtrue\CosClient;

use Overtrue\CosClient\Support\XML;

class JobClient extends Client
{
    protected string $domain = '<uin>.cos-control.<region>.myqcloud.com';

    protected array $requiredConfigKeys = ['uin', 'region'];

    public function __construct(array|Config $config)
    {
        parent::__construct($config);

        $this->setHeader('x-cos-appid', $this->config->get('app_id'));
    }

    public function getJobs(array $query = []): Http\Response
    {
        return $this->get('/jobs', [
            'query' => $query,
        ]);
    }

    public function createJob(array $body): Http\Response
    {
        return $this->post('/jobs', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function describeJob(string $id): Http\Response
    {
        return $this->get(\sprintf('/jobs/%s', $id));
    }

    public function updateJobPriority(string $id, int $priority): Http\Response
    {
        return $this->post(\sprintf('/jobs/%s/priority', $id), [
            'query' => [
                'priority' => $priority,
            ],
        ]);
    }

    public function updateJobStatus(string $id, array $query): Http\Response
    {
        return $this->post(\sprintf('/jobs/%s/status', $id), \compact('query'));
    }
}
