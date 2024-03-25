<?php

namespace Overtrue\CosClient;

use Overtrue\CosClient\Support\XML;

class BucketClient extends Client
{
    protected array $requiredConfigKeys = ['bucket'];

    public function putBucket(array $body = []): Http\Response
    {
        return $this->put('/', empty($body) ? [] : [
            'body' => XML::fromArray($body),
        ]);
    }

    public function headBucket(): Http\Response
    {
        return $this->head('/');
    }

    public function deleteBucket(): Http\Response
    {
        return $this->delete('/');
    }

    public function getObjects(array $query = []): Http\Response
    {
        return $this->get('/', \compact('query'));
    }

    public function getObjectVersions(array $query = []): Http\Response
    {
        return $this->get('/?versions', \compact('query'));
    }

    public function putACL(array $body = [], array $headers = []): Http\Response
    {
        return $this->put('/?acl', \array_filter([
            'headers' => $headers,
            'body' => XML::fromArray($body),
        ]));
    }

    public function getACL(): Http\Response
    {
        return $this->get('/?acl');
    }

    public function putCORS(array $body): Http\Response
    {
        return $this->put('/?cors', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getCORS(): Http\Response
    {
        return $this->get('/?cors');
    }

    public function deleteCORS(): Http\Response
    {
        return $this->delete('/?cors');
    }

    public function putLifecycle(array $body): Http\Response
    {
        return $this->put('/?lifecycle', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getLifecycle(): Http\Response
    {
        return $this->get('/?lifecycle');
    }

    public function deleteLifecycle(): Http\Response
    {
        return $this->delete('/?lifecycle');
    }

    public function putPolicy(array $body): Http\Response
    {
        return $this->put('/?policy', ['json' => $body]);
    }

    public function getPolicy(): Http\Response
    {
        return $this->get('/?policy');
    }

    public function deletePolicy(): Http\Response
    {
        return $this->delete('/?policy');
    }

    public function putReferer(array $body): Http\Response
    {
        return $this->put('/?referer', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getReferer(): Http\Response
    {
        return $this->get('/?referer');
    }

    public function putTagging(array $body): Http\Response
    {
        return $this->put('/?tagging', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getTagging(): Http\Response
    {
        return $this->get('/?tagging');
    }

    public function deleteTagging(): Http\Response
    {
        return $this->delete('/?tagging');
    }

    public function putWebsite(array $body): Http\Response
    {
        return $this->put('/?website', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getWebsite(): Http\Response
    {
        return $this->get('/?website');
    }

    public function deleteWebsite(): Http\Response
    {
        return $this->delete('/?website');
    }

    public function putInventory(string $id, array $body): Http\Response
    {
        return $this->put(\sprintf('/?inventory&id=%s', $id), [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getInventory(string $id): Http\Response
    {
        return $this->get(\sprintf('/?inventory&id=%s', $id));
    }

    public function getInventoryConfigurations(string $nextContinuationToken = null): Http\Response
    {
        return $this->get(\sprintf('/?inventory&continuation-token=%s', $nextContinuationToken));
    }

    public function deleteInventory(string $id): Http\Response
    {
        return $this->delete(\sprintf('/?inventory&id=%s', $id));
    }

    public function putVersioning(array $body): Http\Response
    {
        return $this->put('/?versioning', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getVersioning(): Http\Response
    {
        return $this->get('/?versioning');
    }

    public function putReplication(array $body): Http\Response
    {
        return $this->put('/?replication', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getReplication(): Http\Response
    {
        return $this->get('/?replication');
    }

    public function deleteReplication(): Http\Response
    {
        return $this->delete('/?replication');
    }

    public function putLogging(array $body): Http\Response
    {
        return $this->put('/?logging', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getLogging(): Http\Response
    {
        return $this->get('/?logging');
    }

    public function putAccelerate(array $body): Http\Response
    {
        return $this->put('/?accelerate', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getAccelerate(): Http\Response
    {
        return $this->get('/?accelerate');
    }

    public function putEncryption(array $body): Http\Response
    {
        return $this->put('/?encryption', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function getEncryption(): Http\Response
    {
        return $this->get('/?encryption');
    }

    public function deleteEncryption(): Http\Response
    {
        return $this->delete('/?encryption');
    }
}
