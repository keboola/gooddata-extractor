<?php
namespace Keboola\GoodDataExtractor\Test;

use Keboola\GoodDataExtractor\Provisioning;

class ProvisioningTest extends \PHPUnit\Framework\TestCase
{
    public function testGetBaseUri()
    {
        $storage = new \Keboola\StorageApi\Client([
            'url' => getenv('KBC_URL'),
            'token' => getenv('KBC_TOKEN'),
        ]);
        $this->assertStringEndsWith('keboola.com', Provisioning::getBaseUri($storage));
    }

    public function testGetCredentials()
    {
        $provisioning = new Provisioning(getenv('GD_PROVISIONING_URL'), getenv('KBC_TOKEN'));
        $credentials = $provisioning->getCredentials(getenv('GD_PROVISIONING_PID'));
        $this->assertArrayHasKey('login', $credentials);
        $this->assertNotEmpty($credentials['login']);
        $this->assertArrayHasKey('password', $credentials);
        $this->assertNotEmpty($credentials['password']);
    }
}
