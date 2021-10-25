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

        $username = $config->getCredentials()[0];
        $password = $config->getCredentials()[1];
        $this->getLogger()->info('GoodData credentials obtained from configuration.');

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
