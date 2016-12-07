<?php
/**
 * @package gooddata-extractor
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */

namespace Keboola\AdWordsExtractor;

use Keboola\GoodDataExtractor\Exception;
use Keboola\GoodDataExtractor\WriterCredentials;
use Keboola\StorageApi\Components;

class WriterCredentialsTest extends \PHPUnit_Framework_TestCase
{
    public function testWriterCredentialsSuccess()
    {
        $components = $this->createMock(Components::class);
        $components->method('getConfiguration')->willReturn(json_decode('{
	"id": "dev",
	"name": "dev",
	"description": "",
	"created": "2016-05-20T19:27:50+0200",
	"creatorToken": {
		"id": 5052,
		"description": "someone@keboola.com"
	},
	"configuration": {
		"user": {
			"login": "userlogin",
			"password": "somepassword",
			"uid": "8715a864c348f915935ecc09f19bdd58"
		},
		"project": {
			"pid": "wasc4gjy5sphvlt0wjx5fqys5q6bh38j"
		},
		"dimensions": {},
		"filters": {}
	},
	"rows": [],
	"state": []
}', true));
        $credentials = new WriterCredentials($components);
        $res = $credentials->get('test');
        $this->assertArrayHasKey('username', $res);
        $this->assertArrayHasKey('password', $res);
        $this->assertEquals('userlogin', $res['username']);
        $this->assertEquals('somepassword', $res['password']);
    }

    public function testWriterCredentialsMissing()
    {
        $components = $this->createMock(Components::class);
        $components->method('getConfiguration')->willReturn(json_decode('{
	"id": "dev",
	"name": "dev",
	"description": "",
	"created": "2016-05-20T19:27:50+0200",
	"creatorToken": {
		"id": 5052,
		"description": "someone@keboola.com"
	},
	"configuration": {
		"user": {
			"password": "somepassword",
			"uid": "8715a864c348f915935ecc09f19bdd58"
		},
		"project": {
			"pid": "wasc4gjy5sphvlt0wjx5fqys5q6bh38j"
		},
		"dimensions": {},
		"filters": {}
	},
	"rows": [],
	"state": []
}', true));
        $credentials = new WriterCredentials($components);
        try {
            $credentials->get('test');
            $this->fail();
        } catch (Exception $e) {
            // ok
        }
    }
}
