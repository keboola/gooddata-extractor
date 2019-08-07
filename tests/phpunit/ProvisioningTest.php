<?php

declare(strict_types=1);

namespace Keboola\GoodDataExtractor\Test;

use Keboola\GoodDataExtractor\Provisioning;

class ProvisioningTest extends \PHPUnit\Framework\TestCase
{
    public function testGetBaseUri(): void
    {
        $storage = new \Keboola\StorageApi\Client([
            'url' => KBC_URL,
            'token' => KBC_TOKEN,
        ]);
        $this->assertStringEndsWith('keboola.com', Provisioning::getBaseUri($storage));
    }

    public function testGetCredentials(): void
    {
        $provisioning = new Provisioning(GD_PROVISIONING_URL, KBC_TOKEN);
        $credentials = $provisioning->getCredentials(GD_PROVISIONING_PID);
        $this->assertArrayHasKey('login', $credentials);
        $this->assertNotEmpty($credentials['login']);
        $this->assertArrayHasKey('password', $credentials);
        $this->assertNotEmpty($credentials['password']);
    }
}
