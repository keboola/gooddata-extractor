<?php
/**
 * @package gooddata-extractor
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataExtractor;


class Extractor
{
    /** @var UserStorage */
    protected $userStorage;

    /** @var  Api */
    protected $api;

    public function __construct($clientId, $clientSecret, $developerToken, $refreshToken, $customerId, $folder, $bucket)
    {

        $this->userStorage = new UserStorage(self::$userTables, $folder, $bucket);
    }

    public function extract(array $queries, $since, $until)
    {

    }
}
