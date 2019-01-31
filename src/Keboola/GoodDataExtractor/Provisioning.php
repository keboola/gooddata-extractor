<?php
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

    public function __construct($baseUri, $token)
    {
        $this->baseUri = $baseUri;
        $this->token = $token;

        $this->client = new \GuzzleHttp\Client([
            'handler' => HandlerStack::create([
                'backoffMaxTries' => 10,
            ]),
        ]);
    }

    public function getCredentials($pid)
    {
        try {
            $res = $this->client->request('GET', "{$this->baseUri}/projects/{$pid}/credentials", [
                'headers' => [
                    'X-StorageApi-Token' => $this->token,
                ],
            ]);
            return json_decode($res->getBody(), true);
        } catch (ClientException $e) {
            throw new Exception($e->getResponse()->getStatusCode() == 401
                ? 'Invalid Storage Token'
                : 'User Error from Provisioning: ' . $e->getResponse()->getBody());
        }
    }

    public static function getBaseUri(Client $storage)
    {
        $storageData = $storage->apiGet('storage');
        foreach ($storageData['services'] as $service) {
            if ($service['id'] == 'gooddata-provisioning') {
                return $service['url'];
            }
        }
        throw new \Exception('Provisioning url for gooddata-provisioning not found.');
    }
}
