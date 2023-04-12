<?php

declare(strict_types=1);

namespace Keboola\GoodDataExtractor\Test;

use Keboola\GoodData\Client;
use Keboola\GoodData\Reports;
use Keboola\GoodDataExtractor\Extractor;
use Psr\Log\Test\TestLogger;
use Throwable;

class ExtractorTest extends \PHPUnit\Framework\TestCase
{
    public function testExtractor(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('get')->willReturn(json_decode('{
   "report" : {
      "content" : {
         "domains" : [],
         "definitions" : [
            "/gdc/md/wasc4gjy5sphvlt0wjx5fqys5q6bh38j/obj/2283"
         ]
      },
      "meta" : {
         "author" : "/gdc/account/profile/b564e55fb7b528e3b290268e01342f48"
      }
   }
}', true));
        $client->method('getReports')->willReturnCallback(function () {
            $reports = $this->createMock(Reports::class);
            $reports->method('export')->willReturn('exportLink');
            return $reports;
        });
        $client->method('getToFile')->willReturn('filePath');
        $extractor = new Extractor($client, 'user', 'pass', 'folder');
        $extractor->extract(['/gdc/md/report']);
        $this->assertTrue(true);
    }

    public function testExtractorBadReport(): void
    {
        $client = new Client('https://' . getenv('EX_GD_HOST'));

        $extractor = new Extractor(
            $client,
            (string) getenv('EX_GD_USERNAME'),
            (string) getenv('EX_GD_PASSWORD'),
            'folder'
        );

        $testLogger = new TestLogger();
        $extractor->setLogger($testLogger);

        try {
            $extractor->extract(['/gdc/md/doesnotexist']);
            $this->fail('Exception should be thrown');
        } catch (Throwable $e) {
            $this->assertStringContainsString(
                'Failed to download report \'/gdc/md/doesnotexist\' after 5 retries',
                $e->getMessage()
            );
        }

        for ($i = 1; $i < 5; $i++) {
            $this->assertTrue(
                $testLogger->hasInfo('Name doesnotexist doesn\'t exist.. Retrying... [' . $i . 'x]')
            );
        }
    }
}
