<?php

declare(strict_types=1);

namespace Keboola\GoodDataExtractor;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class WriterClient
{
    /** @var \GuzzleHttp\Client  */
    protected $client;

    public function __construct()
    {
        $handlerStack = HandlerStack::create();

        /** @noinspection PhpUnusedParameterInspection */
        $handlerStack->push(Middleware::retry(
            function ($retries, RequestInterface $request, ?ResponseInterface $response = null, ?string $error = null) {
                return $response && $response->getStatusCode() === 503;
            },
            function ($retries) {
                return rand(60, 600) * 1000;
            }
        ));
        /** @noinspection PhpUnusedParameterInspection */
        $handlerStack->push(Middleware::retry(
            function ($retries, RequestInterface $request, ?ResponseInterface $response = null, ?string $error = null) {
                if ($retries >= 10) {
                    return false;
                } elseif ($response && $response->getStatusCode() > 499) {
                    return true;
                } elseif ($error) {
                    return true;
                } else {
                    return false;
                }
            },
            function ($retries) {
                return (int) pow(2, $retries - 1) * 1000;
            }
        ));

        $this->client = new \GuzzleHttp\Client([
            'handler' => $handlerStack,
        ]);
    }

    public function get(string $url, string $token): array
    {
        try {
            $res = $this->client->request('GET', $url, [
                'headers' => [
                    'X-StorageApi-Token' => $token,
                ],
            ]);
            return json_decode((string) $res->getBody(), true);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            if (!$response) {
                throw new Exception('Error from Provisioning: ' . $e->getMessage());
            }
            throw new Exception($response->getStatusCode() === 401
                ? 'Invalid StorageApi Token'
                : 'User Error from StorageApi: ' . $response->getBody());
        }
    }
}
