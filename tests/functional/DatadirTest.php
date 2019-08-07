<?php

declare(strict_types=1);

namespace Keboola\GoodDataExtractor\Tests\Functional;

use Keboola\DatadirTests\AbstractDatadirTestCase;
use Keboola\DatadirTests\DatadirTestSpecification;
use Keboola\GoodData\Client;
use Keboola\GoodDataExtractor\Test\IntegrationTest;

class DatadirTest extends AbstractDatadirTestCase
{

    public function testRun(): void
    {
        foreach (['EX_GD_USERNAME', 'EX_GD_PASSWORD', 'EX_GD_PROJECT'] as $env) {
            if (getenv($env) === false) {
                throw new \Exception("$env not set in env.");
            }
        }

        $client = new Client();
        $client->login(getenv('EX_GD_USERNAME'), getenv('EX_GD_PASSWORD'));
        $reportUri = IntegrationTest::createReport($client, getenv('EX_GD_PROJECT'));
        $reportId = substr($reportUri, strrpos($reportUri, '/') + 1);

        $config = [
            'action' => 'run',
            'parameters' => [
                "username" => getenv('EX_GD_USERNAME'),
                "#password" => getenv('EX_GD_PASSWORD'),
                "reports" => [$reportUri],
            ],
        ];

        $specification = new DatadirTestSpecification(
            __DIR__ . '/run/source/data',
            0,
            '',
            '',
        );
        $tempDatadir = $this->getTempDatadir($specification);
        file_put_contents($tempDatadir->getTmpFolder() . '/config.json', \GuzzleHttp\json_encode($config));
        $process = $this->runScript($tempDatadir->getTmpFolder());
        $this->assertMatchesSpecification($specification, $process, $tempDatadir->getTmpFolder());
        $this->assertFileExists($tempDatadir->getTmpFolder() . "/out/tables/$reportId.csv");
        $csv = file($tempDatadir->getTmpFolder() . "/out/tables/$reportId.csv");
        $this->assertEquals('"Id","Name"', trim($csv[0]));
    }
}
