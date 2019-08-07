<?php

declare(strict_types=1);

namespace Keboola\GoodDataExtractor;

class Writer
{
    /** @var WriterClient  */
    private $client;
    /** @var string  */
    private $baseUri = 'https://syrup.keboola.com/gooddata-writer';
    /** @var string  */
    private $token;

    public function __construct(WriterClient $client, string $token)
    {
        $this->client = $client;
        $this->token = $token;
    }

    public function getUserCredentials(string $writerId): array
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
            'password' => $response['user']['password'],
        ];
    }
}
