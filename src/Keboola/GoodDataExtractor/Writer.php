<?php
/**
 * @package gooddata-extractor
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataExtractor;

use GuzzleHttp\Client;

class Writer
{
    /** @var Client  */
    private $client;
    private $baseUri = "https://syrup.keboola.com/gooddata-writer";
    private $token;

    public function __construct(WriterClient $client, $token)
    {
        $this->client = $client;
        $this->token = $token;
    }

    public function getUserCredentials($writerId)
    {
        $response = $this->client->get("{$this->baseUri}/v2/$writerId?include=user", $this->token);
        if (!isset($response['user']['email'])) {
            throw new Exception('User email is missing from writer\'s configuration');
        }
        if (!isset($response['user']['password'])) {
            throw new Exception('User password is missing from writer\'s configuration');
        }
        return [
            'username' => $response['user']['email'],
            'password' => $response['user']['password']
        ];
    }
}
