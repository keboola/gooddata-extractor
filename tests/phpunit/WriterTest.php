<?php /** @noinspection PhpParamsInspection */

declare(strict_types=1);

namespace Keboola\GoodDataExtractor\Test;

use Keboola\GoodDataExtractor\Exception;
use Keboola\GoodDataExtractor\Writer;
use Keboola\GoodDataExtractor\WriterClient;

class WriterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return WriterClient&\PHPUnit\Framework\MockObject\MockObject
     */
    private function createWriterClientMock(): \PHPUnit\Framework\MockObject\MockObject
    {
        return $this->createMock(WriterClient::class);
    }
    public function testWriterCredentialsSuccess(): void
    {
        $client = $this->createWriterClientMock();
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

    public function testWriterCredentialsMissing(): void
    {
        $client = $this->createWriterClientMock();
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
        $this->assertTrue(true);
    }
}
