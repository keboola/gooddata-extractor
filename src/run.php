<?php
/**
 * @package gooddata-extractor
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */

set_error_handler(
    function ($errno, $errstr, $errfile, $errline, array $errcontext) {
        if (0 === error_reporting()) {
            return false;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
);

defined('KBC_URL') || define('KBC_URL', getenv('KBC_URL')? getenv('KBC_URL') : 'https://connection.keboola.com');
defined('KBC_TOKEN') || define('KBC_TOKEN', getenv('KBC_TOKEN')? getenv('KBC_TOKEN') : 'token');

require_once(dirname(__FILE__) . "/../vendor/autoload.php");
$arguments = getopt("d::", array("data::"));
if (!isset($arguments['data'])) {
    print "Data folder not set.";
    exit(1);
}
$config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($arguments['data'] . "/config.yml"));

if (!isset($config['parameters']['writer_id'])) {
    if (!isset($config['parameters']['username']) || !isset($config['parameters']['#password'])) {
        print("Missing either parameter 'writer_id' or 'username' and '#password'");
        exit(1);
    }
}

if (!isset($config['parameters']['bucket'])) {
    print("Missing parameter 'bucket'");
    exit(1);
}

if (!isset($config['parameters']['reports'])) {
    print("Missing parameter 'reports'");
    exit(1);
}

if (!is_array($config['parameters']['reports'])) {
    print "Parameter 'reports' has to be array";
    exit(1);
}

if (!count($config['parameters']['reports'])) {
    print "Parameter 'reports' is empty";
    exit(1);
}

if (!file_exists("{$arguments['data']}/out")) {
    mkdir("{$arguments['data']}/out");
}
if (!file_exists("{$arguments['data']}/out/tables")) {
    mkdir("{$arguments['data']}/out/tables");
}

try {
    if (isset($config['parameters']['writer_id'])) {
        $writer = new \Keboola\GoodDataExtractor\Writer(
            new \Keboola\GoodDataExtractor\WriterClient(),
            KBC_TOKEN
        );
        $creds = $writer->getUserCredentials($config['parameters']['writer_id']);
        $username = $creds['username'];
        $password = $creds['password'];
    } else {
        $username = $config['parameters']['username'];
        $password = $config['parameters']['#password'];
    }
    $app = new \Keboola\GoodDataExtractor\Extractor(
        new \Keboola\GoodData\Client(),
        $username,
        $password,
        "{$arguments['data']}/out/tables",
        $config['parameters']['bucket']
    );
    $app->extract($config['parameters']['reports']);

    exit(0);
} catch (\Keboola\GoodDataExtractor\Exception $e) {
    print $e->getMessage();
    exit(1);
} catch (\Exception $e) {
    print $e->getMessage();
    print $e->getTraceAsString();
    exit(2);
}
