<?php

declare(strict_types=1);

namespace Keboola\GoodDataExtractor\Test\Functional;

use Keboola\DatadirTests\AbstractDatadirTestCase;
use Keboola\DatadirTests\DatadirTestSpecification;
use Keboola\GoodData\Client;
use Keboola\GoodDataExtractor\Test\IntegrationTest;

class DatadirTest extends AbstractDatadirTestCase
{

    public function testRun(): void
    {
        $client = new Client();
        $client->login(EX_GD_USERNAME, EX_GD_PASSWORD);
        $reportUri = IntegrationTest::createReport($client, EX_GD_PROJECT);
        $reportId = substr($reportUri, strrpos($reportUri, '/') + 1);

        $config = [
            'action' => 'run',
            'parameters' => [
                'username' => EX_GD_USERNAME,
                '#password' => EX_GD_PASSWORD,
                'reports' => [$reportUri],
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
        $this->assertNotFalse($csv);
        $this->assertEquals('"Id","Name"', trim($csv[0]));
    }
}
