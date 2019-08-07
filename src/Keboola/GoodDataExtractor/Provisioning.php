<?php

declare(strict_types=1);

namespace Keboola\GoodDataExtractor;

use GuzzleHttp\Exception\ClientException;
use Keboola\StorageApi\Client;
use Keboola\StorageApi\HandlerStack;

class Provisioning
{
    /** @var string */
    protected $baseUri;
    /** @var string */
    protected $token;
    /** @var \GuzzleHttp\Client */
    protected $client;

    public function __construct(string $baseUri, string $token)
    {
        $this->baseUri = $baseUri;
        $this->token = $token;

        $this->client = new \GuzzleHttp\Client([
            'handler' => HandlerStack::create([
                'backoffMaxTries' => 10,
            ]),
        ]);
    }

    public function getCredentials(string $pid): array
    {
        try {
            $res = $this->client->request('GET', "{$this->baseUri}/projects/{$pid}/credentials", [
                'headers' => [
                    'X-StorageApi-Token' => $this->token,
                ],
            ]);
            return json_decode((string) $res->getBody(), true);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            if (!$response) {
                throw new Exception('Error from Provisioning: ' . $e->getMessage());
            }
            throw new Exception($response->getStatusCode() === 401
                ? 'Invalid Storage Token'
                : 'User Error from Provisioning: ' . $response->getBody());
        }
    }

    public static function getBaseUri(Client $storage): string
    {
        $storageData = $storage->apiGet('storage');
        foreach ($storageData['services'] as $service) {
            if ($service['id'] === 'gooddata-provisioning') {
                return $service['url'];
            }
        }
        throw new \Exception('Provisioning url for gooddata-provisioning not found.');
    }
}
