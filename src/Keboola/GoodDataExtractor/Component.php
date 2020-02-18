<?php

declare(strict_types=1);

namespace Keboola\GoodDataExtractor;

use Keboola\Component\BaseComponent;

class Component extends BaseComponent
{
    protected function run(): void
    {
        /** @var Config $config */
        $config = $this->getConfig();

        if (!file_exists($this->getDataDir() . '/out')) {
            mkdir($this->getDataDir() . '/out');
        }
        if (!file_exists($this->getDataDir() . '/out/tables')) {
            mkdir($this->getDataDir() . '/out/tables');
        }

        defined('KBC_URL') || define('KBC_URL', getenv('KBC_URL')
            ? getenv('KBC_URL') : 'https://connection.keboola.com');
        defined('KBC_TOKEN') || define('KBC_TOKEN', getenv('KBC_TOKEN')
            ? getenv('KBC_TOKEN') : 'token');

        if ($config->getPid()) {
            // Extractor will get credentials from Provisioning
            $storage = new \Keboola\StorageApi\Client([
                'url' => KBC_URL,
                'token' => KBC_TOKEN,
            ]);
            $provisioningUrl = Provisioning::getBaseUri($storage);
            $provisioning = new Provisioning($provisioningUrl, KBC_TOKEN);

            $credentials = $provisioning->getCredentials($config->getPid());
            $username = $credentials['login'];
            $password = $credentials['password'];
            $this->getLogger()->info('GoodData credentials obtained from Provisioning.');
        } elseif ($config->getWriterId()) {
            // Extractor will get credentials from Writer configuration (deprecated option)
            $writer = new \Keboola\GoodDataExtractor\Writer(
                new \Keboola\GoodDataExtractor\WriterClient(),
                KBC_TOKEN
            );
            $creds = $writer->getUserCredentials($config->getWriterId());
            $username = $creds['username'];
            $password = $creds['password'];
            $this->getLogger()->info('GoodData credentials obtained directly from Writer.');
        } else {
            $username = $config->getCredentials()[0];
            $password = $config->getCredentials()[1];
            $this->getLogger()->info('GoodData credentials obtained from configuration.');
        }
        $url = 'https://' . $config->getHost();
        $this->getLogger()->info("GoodData backend: {$url}, username: {$username}");
        $app = new \Keboola\GoodDataExtractor\Extractor(
            new \Keboola\GoodData\Client($url),
            $username,
            $password,
            $this->getDataDir() . '/out/tables'
        );
        $app->setLogger($this->getLogger());
        $app->extract($config->getReports());
    }

    protected function getConfigClass(): string
    {
        return Config::class;
    }

    protected function getConfigDefinitionClass(): string
    {
        return ConfigDefinition::class;
    }
}
