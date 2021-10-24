<?php

declare(strict_types=1);

namespace Keboola\GoodDataExtractor;

use Keboola\Component\Config\BaseConfig;
use Keboola\Component\UserException;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Config extends BaseConfig
{
    public function __construct(array $config, ?ConfigurationInterface $configDefinition = null)
    {
        parent::__construct($config, $configDefinition);

        if (!count($this->getCredentials())) {
            throw new UserException("Missing 'username' and '#password' from configuration");
        }
        if (!count($this->getReports())) {
            throw new UserException("Parameter 'reports' from configuration does not contain any report");
        }
    }

    public function getHost(): string
    {
        return $this->getValue(['parameters', 'host'], 'secure.gooddata.com');
    }

    public function getCredentials(): array
    {
        $username = $this->getValue(['parameters', 'username'], '');
        $password = $this->getValue(['parameters', '#password'], '');
        if (!$username && !$password) {
            return [];
        }
        return [$username, $password];
    }

    public function getReports(): array
    {
        return $this->getValue(['parameters', 'reports'], []);
    }
}
