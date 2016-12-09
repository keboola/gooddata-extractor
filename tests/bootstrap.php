<?php
/**
 * @package gooddata-extractor
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */

defined('EX_GD_USERNAME') || define('EX_GD_USERNAME', getenv('EX_GD_USERNAME')
    ? getenv('EX_GD_USERNAME') : 'gd username');

defined('EX_GD_PASSWORD') || define('EX_GD_PASSWORD', getenv('EX_GD_PASSWORD')
    ? getenv('EX_GD_PASSWORD') : 'gd password');

defined('EX_GD_PROJECT') || define('EX_GD_PROJECT', getenv('EX_GD_PROJECT')
    ? getenv('EX_GD_PROJECT') : 'gd project');

require_once __DIR__ . '/../vendor/autoload.php';
