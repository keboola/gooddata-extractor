<?php
/**
 * @package gooddata-extractor
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataExtractor;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class WriterClient
{
    protected $client;

    public function __construct()
    {
        $handlerStack = HandlerStack::create();

        /** @noinspection PhpUnusedParameterInspection */
        $handlerStack->push(Middleware::retry(
            function ($retries, RequestInterface $request, ResponseInterface $response = null, $error = null) {
                return $response && $response->getStatusCode() == 503;
            },
            function ($retries) {
                return rand(60, 600) * 1000;
            }
        ));
        /** @noinspection PhpUnusedParameterInspection */
        $handlerStack->push(Middleware::retry(
            function ($retries, RequestInterface $request, ResponseInterface $response = null, $error = null) {
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
            'handler' => $handlerStack
        ]);
    }

    public function get($url, $token)
    {
        try {
            $res = $this->client->request('GET', $url, [
                'headers' => [
                    'X-StorageApi-Token' => $token
                ]
            ]);
            return json_decode($res->getBody(), true);
        } catch (ClientException $e) {
            throw new Exception($e->getResponse()->getStatusCode() == 401
                ? 'Invalid StorageApi Token'
                : 'User Error from StorageApi: ' . $e->getResponse()->getBody()
            );
        }
    }
}
