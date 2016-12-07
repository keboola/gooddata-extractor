<?php
/**
 * @package gooddata-extractor
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataExtractor;

use Keboola\StorageApi\Components;

class WriterCredentials
{
    /** @var Components  */
    private $client;

    public function __construct(Components $client)
    {
        $this->client = $client;
    }

    public function get($writerId)
    {
        $res = $this->client->getConfiguration('gooddata-writer', $writerId);
        if (!isset($res['configuration']['user']['login'])) {
            throw new Exception('User login is missing from writer\'s configuration');
        }
        if (!isset($res['configuration']['user']['password'])) {
            throw new Exception('User password is missing from writer\'s configuration');
        }
        return [
            'username' => $res['configuration']['user']['login'],
            'password' => $res['configuration']['user']['password']
        ];
    }
}
