<?php
/**
 * @package gooddata-extractor
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */

namespace Keboola\GoodDataExtractor\Test;

use Keboola\GoodDataExtractor\Exception;
use Keboola\GoodDataExtractor\Writer;
use Keboola\GoodDataExtractor\WriterClient;

class WriterTest extends \PHPUnit\Framework\TestCase
{
    public function testWriterCredentialsSuccess()
    {
        $client = $this->createMock(WriterClient::class);
        $client->method('get')->willReturn(json_decode('{
	"id": "dev",
	"status": "ready",
	"user": {
        "email": "userlogin",
        "password": "somepassword",
        "uid": "8715a864c348f915935ecc09f19bdd58"
    }
}', true));
        $writer = new Writer($client, 'token');
        $res = $writer->getUserCredentials('test');
        $this->assertArrayHasKey('username', $res);
        $this->assertArrayHasKey('password', $res);
        $this->assertEquals('userlogin', $res['username']);
        $this->assertEquals('somepassword', $res['password']);
    }

    public function testWriterCredentialsMissing()
    {
        $client = $this->createMock(WriterClient::class);
        $client->method('get')->willReturn(json_decode('{
	"id": "dev",
	"status": "ready",
    "user": {
        "password": "somepassword",
        "uid": "8715a864c348f915935ecc09f19bdd58"
    },
    "project": {
        "pid": "wasc4gjy5sphvlt0wjx5fqys5q6bh38j"
    }
}', true));
        $writer = new Writer($client, 'token');
        try {
            $writer->getUserCredentials('test');
            $this->fail();
        } catch (Exception $e) {
            // ok
        }
    }
}
