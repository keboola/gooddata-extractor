<?php
/**
 * @package gooddata-extractor
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */

namespace Keboola\GoodDataExtractor\Test;

use Keboola\GoodData\Client;
use Keboola\GoodData\Reports;
use Keboola\GoodDataExtractor\Extractor;

class ExtractorTest extends \PHPUnit\Framework\TestCase
{
    public function testExtractor()
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
}
