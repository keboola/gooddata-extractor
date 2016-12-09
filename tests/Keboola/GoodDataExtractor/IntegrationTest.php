<?php
/**
 * @package gooddata-extractor
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */

namespace Keboola\GoodDataExtractor\Test;

use Keboola\GoodData\Client;
use Keboola\GoodData\Exception;
use Keboola\GoodData\WebDav;
use Keboola\GoodDataExtractor\Extractor;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testIntegration()
    {
        $client = new Client();
        $client->login(EX_GD_USERNAME, EX_GD_PASSWORD);
        $this->cleanUpProject($client, EX_GD_PROJECT);
        $client->getProjectModel()->updateProject(
            EX_GD_PROJECT,
            json_decode(file_get_contents(__DIR__.'/data/model.json'), true)
        );
        $this->loadData($client, EX_GD_PROJECT);
        $report1 = $this->createReport($client, EX_GD_PROJECT);
        $report2 = $this->createReport($client, EX_GD_PROJECT);

        $dir = sys_get_temp_dir() . '/' . uniqid();
        mkdir($dir);
        $app = new Extractor(
            new Client(),
            EX_GD_USERNAME,
            EX_GD_PASSWORD,
            $dir,
            'bucket'
        );
        $app->extract([$report1, $report2]);

        $reportId1 = substr($report1, strrpos($report1, '/') + 1);
        $reportId2 = substr($report2, strrpos($report2, '/') + 1);
        $sourceFile = file(__DIR__.'/data/data.csv');

        $this->assertFileExists("$dir/bucket.$reportId1.csv");
        $file1 = file("$dir/bucket.$reportId1.csv");
        $this->assertCount(count($sourceFile), $file1);
        $this->assertEquals(trim($sourceFile[0]), trim($file1[0]));
        $this->assertEquals(trim($sourceFile[5]), trim($file1[5]));
        $this->assertFileExists("$dir/bucket.$reportId2.csv");
        $file2 = file("$dir/bucket.$reportId2.csv");
        $this->assertCount(count($sourceFile), $file2);
        $this->assertEquals(trim($sourceFile[0]), trim($file2[0]));
        $this->assertEquals(trim($sourceFile[4]), trim($file2[4]));
    }

    public static function createReport(Client $client, $pid)
    {
        $attribute1 = $client->getDatasets()->getUriForIdentifier($pid, "label.categories.id");
        $attribute2 = $client->getDatasets()->getUriForIdentifier($pid, "label.categories.name");

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
            "report" => [
                "content" => [
                    "domains" => [],
                    "definitions" => [
                        $result['uri']
                    ]
                ],
                "meta" => [
                    "tags" => "",
                    "deprecated" => "0",
                    "summary" => "",
                    "title" => 'Test Report ' . uniqid()
                ]
            ]
        ]);
        return $result['uri'];
    }

    private function loadData(Client $client, $pid)
    {
        $dirName = uniqid();
        $webDav = new WebDav(EX_GD_USERNAME, EX_GD_PASSWORD);
        $webDav->createFolder($dirName);
        $webDav->upload(__DIR__.'/data/data.csv', $dirName);
        $webDav->upload(__DIR__.'/data/upload_info.json', $dirName);
        $client->getDatasets()->loadData($pid, $dirName);
    }

    private function cleanUpProject(Client $client, $pid)
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
}
