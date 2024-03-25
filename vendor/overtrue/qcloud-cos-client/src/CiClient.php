<?php

namespace Overtrue\CosClient;

use Overtrue\CosClient\Support\XML;

class CiClient extends Client
{
    protected string $domain = '<bucket>-<app_id>.ci.<region>.myqcloud.com';

    protected array $requiredConfigKeys = ['bucket'];

    public function detectImage(array $body): Http\Response
    {
        return $this->post('/image/auditing', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getImageJob(string $jobId): Http\Response
    {
        return $this->get(\sprintf('/image/auditing/%s', $jobId));
    }

    public function detectVideo(array $body): Http\Response
    {
        return $this->post('/video/auditing', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getVideoJob(string $jobId): Http\Response
    {
        return $this->get(\sprintf('/video/auditing/%s', $jobId));
    }

    public function detectAudio(array $body): Http\Response
    {
        return $this->post('/audio/auditing', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getAudioJob(string $jobId): Http\Response
    {
        return $this->get(\sprintf('/audio/auditing/%s', $jobId));
    }

    public function detectText(array $body): Http\Response
    {
        return $this->post('/text/auditing', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getTextJob(string $jobId): Http\Response
    {
        return $this->get(\sprintf('/text/auditing/%s', $jobId));
    }

    public function detectDocument(array $body): Http\Response
    {
        return $this->post('/document/auditing', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getDocumentJob(string $jobId): Http\Response
    {
        return $this->get(\sprintf('/document/auditing/%s', $jobId));
    }

    public function detectWebPage(array $body): Http\Response
    {
        return $this->post('/webpage/auditing', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getWebPageJob(string $jobId): Http\Response
    {
        return $this->get(\sprintf('/webpage/auditing/%s', $jobId));
    }

    public function detectLiveVideo(array $body): Http\Response
    {
        return $this->post('/video/auditing', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getLiveVideoJob(string $jobId): Http\Response
    {
        return $this->get(\sprintf('/video/auditing/%s', $jobId));
    }

    public function deleteLiveVideoJob(string $jobId): Http\Response
    {
        return $this->post(\sprintf('/video/cancel_auditing/%s', $jobId));
    }

    public function reportBadcase(array $body): Http\Response
    {
        return $this->post('/report/badcase', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function detectVirus(array $body): Http\Response
    {
        return $this->post('/virus/detect', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getVirusJob(string $jobId): Http\Response
    {
        return $this->get(\sprintf('/virus/detect/%s', $jobId));
    }
}
