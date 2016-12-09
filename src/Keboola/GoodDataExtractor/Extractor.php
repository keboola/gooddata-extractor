<?php
/**
 * @package gooddata-extractor
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataExtractor;

use Keboola\GoodData\Client;
use Keboola\GoodData\Exception as GoodDataException;

class Extractor
{
    /** @var Client  */
    protected $gdClient;
    protected $folder;
    protected $bucket;

    public function __construct(Client $gdClient, $username, $password, $folder, $bucket)
    {
        $this->gdClient = $gdClient;
        $this->gdClient->login($username, $password);
        $this->folder = $folder;
        $this->bucket = $bucket;
    }

    public function extract(array $reports)
    {
        foreach ($reports as $uri) {
            if (substr($uri, 0, 8) != '/gdc/md/') {
                throw new Exception("Report $uri is not valid report uri");
            }
            $pid = explode('/', substr($uri, 8))[0];
            $reportId = substr($uri, strrpos($uri, '/') + 1);
            $filename = "{$this->folder}/{$this->bucket}.{$reportId}.csv";

            $this->download($pid, $uri, $filename);
        }
    }

    public function download($pid, $uri, $filename)
    {
        try {
            $report = $this->gdClient->get($uri);
            if (!isset($report['report']['content']['definitions'][0])) {
                throw new Exception("Report '{$uri}' has no definitions to export");
            }
            $reportDefinitions = $report['report']['content']['definitions'];
            $reportDefinitionUri = array_pop($reportDefinitions);

            $responseUri = $this->gdClient->getReports()->export($pid, $reportDefinitionUri);

            $this->gdClient->getToFile($responseUri, $filename);
        } catch (GoodDataException $e) {
            throw new Exception($e->getMessage(), 400, $e);
        }
    }
}
