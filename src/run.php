<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$logger = new \Keboola\Component\Logger();
try {
    $app = new \Keboola\GoodDataExtractor\Component($logger);
    $app->execute();
    exit(0);
} catch (\Throwable $e) {
    if (
        $e instanceof \Keboola\Component\UserException ||
        $e instanceof \Keboola\GoodData\Exception ||
        $e instanceof \Keboola\GoodDataExtractor\Exception
    ) {
        $logger->error($e->getMessage());
        exit(1);
    }

    $logger->critical(
        get_class($e) . ':' . $e->getMessage(),
        [
            'errFile' => $e->getFile(),
            'errLine' => $e->getLine(),
            'errCode' => $e->getCode(),
            'errTrace' => $e->getTraceAsString(),
            'errPrevious' => $e->getPrevious() ? get_class($e->getPrevious()) : '',
        ]
    );
    exit(2);
}
