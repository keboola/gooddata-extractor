<?php

declare(strict_types=1);

namespace Keboola\GoodDataExtractor;

use Keboola\GoodData\Client;
use Keboola\GoodData\Exception as GoodDataException;
use Psr\Log\LoggerInterface;

class Extractor
{
    /** @var Client  */
    protected $gdClient;
    /** @var string  */
    protected $folder;
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(Client $gdClient, string $username, string $password, string $folder)
    {
        $this->gdClient = $gdClient;
        $this->gdClient->login($username, $password);
        $this->folder = $folder;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    protected function log(string $message): void
    {
        if (!empty($this->logger)) {
            $this->logger->info($message);
        }
    }

    public function extract(array $reports): void
    {
        foreach ($reports as $uri) {
            if (substr($uri, 0, 8) !== '/gdc/md/') {
                throw new Exception("Report $uri is not valid report uri");
            }
            $pid = explode('/', substr($uri, 8))[0];
            $reportId = substr($uri, strrpos($uri, '/') + 1);
            $filename = "{$this->folder}/{$reportId}.csv";

            $this->download($pid, $uri, $filename);
        }
    }

    public function download(string $pid, string $uri, string $filename): void
    {
        try {
            $this->log("Downloading report '{$uri}'");
            $report = $this->gdClient->get($uri);
            if (!isset($report['report']['content']['definitions'][0])) {
                throw new Exception("Report '{$uri}' has no definitions to export");
            }
            $reportDefinitions = $report['report']['content']['definitions'];
            $reportDefinitionUri = array_pop($reportDefinitions);
            $this->log("Found report definition '{$reportDefinitionUri}'");

            $responseUri = $this->gdClient->getReports()->export($pid, $reportDefinitionUri);

            $this->log("Downloading report data from '{$responseUri}'");
            $this->gdClient->getToFile($responseUri, $filename);
        } catch (GoodDataException $e) {
            throw new Exception($e->getMessage(), 400, $e);
        }
    }
}
