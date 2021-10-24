<?php

declare(strict_types=1);

namespace Keboola\GoodDataExtractor\Test;

use Keboola\GoodData\Client;
use Keboola\GoodData\Exception;
use Keboola\GoodData\WebDav;
use Keboola\GoodDataExtractor\Extractor;

class IntegrationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider hostDataProvider
     */
    public function testIntegration(string $hostName, string $username, string $password, string $project): void
    {
        $model = file_get_contents(__DIR__ . '/data/model.json');
        if (!$model) {
            throw new \Exception('Model file could not be read.');
        }
        $host = 'https://' . $hostName;
        $client = new Client($host);
        $client->login($username, $password);
        $this->cleanUpProject($client, $project);
        $client->getProjectModel()->updateProject($project, json_decode($model, true));
        $dirName = $this->prepareWebDav($host, $username, $password);
        $client->getDatasets()->loadData($project, $dirName);
        $report1 = $this->createReport($client, $project);
        $report2 = $this->createReport($client, $project);

        $dir = sys_get_temp_dir() . '/' . uniqid();
        mkdir($dir);
        $app = new Extractor(
            new Client($host),
            $username,
            $password,
            $dir
        );
        $app->extract([$report1, $report2]);

        $reportId1 = substr($report1, strrpos($report1, '/') + 1);
        $reportId2 = substr($report2, strrpos($report2, '/') + 1);
        $sourceFile = file(__DIR__ . '/data/data.csv');
        if (!$sourceFile) {
            throw new \Exception('Data csv for integration test invalid');
        }

        $this->assertFileExists("$dir/$reportId1.csv");
        $file1 = file("$dir/$reportId1.csv");
        $this->assertNotFalse($file1);
        $this->assertCount(count($sourceFile), $file1);
        $this->assertEquals(trim($sourceFile[0]), trim($file1[0]));
        $this->assertEquals(trim($sourceFile[5]), trim($file1[5]));
        $this->assertFileExists("$dir/$reportId2.csv");
        $file2 = file("$dir/$reportId2.csv");
        $this->assertNotFalse($file2);
        $this->assertCount(count($sourceFile), $file2);
        $this->assertEquals(trim($sourceFile[0]), trim($file2[0]));
        $this->assertEquals(trim($sourceFile[4]), trim($file2[4]));
    }

    public static function createReport(Client $client, string $pid): string
    {
        $attribute1 = $client->getDatasets()->getUriForIdentifier($pid, 'label.categories.id');
        $attribute2 = $client->getDatasets()->getUriForIdentifier($pid, 'label.categories.name');

        $definition = json_decode('
{
   "reportDefinition" : {
      "content" : {
         "grid" : {
            "sort" : {
               "columns" : [],
               "rows" : []
            },
            "columnWidths" : [],
            "columns" : [],
            "metrics" : [],
            "rows" : [
               {
                  "attribute" : {
                     "alias" : "",
                     "totals" : [],
                     "uri" : "'.$attribute1.'"
                  }
               },
               {
                  "attribute" : {
                     "alias" : "",
                     "totals" : [],
                     "uri" : "'.$attribute2.'"
                  }
               }
            ]
         },
         "format" : "grid",
         "filters" : []
      },
      "meta" : {
         "tags" : "",
         "deprecated" : "0",
         "summary" : "",
         "title" : "Test Report Definition",
         "category" : "reportDefinition"
      }
   }
}', true);
        $result = $client->post("/gdc/md/$pid/obj", $definition);
        $result = $client->post("/gdc/md/$pid/obj", [
            'report' => [
                'content' => [
                    'domains' => [],
                    'definitions' => [
                        $result['uri'],
                    ],
                ],
                'meta' => [
                    'tags' => '',
                    'deprecated' => '0',
                    'summary' => '',
                    'title' => 'Test Report ' . uniqid(),
                ],
            ],
        ]);
        return $result['uri'];
    }

    private function prepareWebDav(string $host, string $username, string $password): string
    {
        $uri = $host . '/gdc/uploads/';
        $dirName = uniqid();
        $webDav = new WebDav($username, $password, $uri);
        $webDav->createFolder($dirName);
        $webDav->upload(__DIR__ . '/data/data.csv', $dirName);
        $webDav->upload(__DIR__ . '/data/upload_info.json', $dirName);
        return $dirName;
    }

    private function cleanUpProject(Client $client, string $pid): void
    {
        do {
            $error = false;
            $datasets = $client->get("/gdc/md/$pid/data/sets");
            foreach ($datasets['dataSetsInfo']['sets'] as $dataset) {
                try {
                    $client->getDatasets()->executeMaql(
                        $pid,
                        'DROP ALL IN {' . $dataset['meta']['identifier'] . '} CASCADE'
                    );
                } catch (Exception $e) {
                    $error = true;
                }
            }
        } while ($error);

        $folders = $client->get("/gdc/md/$pid/query/folders");
        foreach ($folders['query']['entries'] as $folder) {
            try {
                $client->getDatasets()->executeMaql(
                    $pid,
                    'DROP {'.$folder['identifier'].'};'
                );
            } catch (Exception $e) {
            }
        }
        $dimensions = $client->get("/gdc/md/$pid/query/dimensions");
        foreach ($dimensions['query']['entries'] as $folder) {
            try {
                $client->getDatasets()->executeMaql($pid, 'DROP {'.$folder['identifier'].'};');
            } catch (Exception $e) {
            }
        }
    }

    public function hostDataProvider(): array
    {
        return [
            [
                EX_GD_HOST,
                EX_GD_USERNAME,
                EX_GD_PASSWORD,
                EX_GD_PROJECT,
            ],
        ];
    }
}
