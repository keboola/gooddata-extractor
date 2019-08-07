<?php

declare(strict_types=1);

defined('EX_GD_USERNAME') || define('EX_GD_USERNAME', getenv('EX_GD_USERNAME')
    ? getenv('EX_GD_USERNAME') : 'gd username');

defined('EX_GD_PASSWORD') || define('EX_GD_PASSWORD', getenv('EX_GD_PASSWORD')
    ? getenv('EX_GD_PASSWORD') : 'gd password');

defined('EX_GD_PROJECT') || define('EX_GD_PROJECT', getenv('EX_GD_PROJECT')
    ? getenv('EX_GD_PROJECT') : 'gd project');

defined('EX_GD_HOST') || define('EX_GD_HOST', getenv('EX_GD_HOST')
    ? getenv('EX_GD_HOST') : 'gd host');

defined('EX_GD_REPORT_URI') || define('EX_GD_REPORT_URI', getenv('EX_GD_REPORT_URI')
    ? getenv('EX_GD_REPORT_URI') : 'gd report uri');

defined('EX_GD_ALT_USERNAME') || define('EX_GD_ALT_USERNAME', getenv('EX_GD_ALT_USERNAME')
    ? getenv('EX_GD_ALT_USERNAME') : 'gd alt username');

defined('EX_GD_ALT_PASSWORD') || define('EX_GD_ALT_PASSWORD', getenv('EX_GD_ALT_PASSWORD')
    ? getenv('EX_GD_ALT_PASSWORD') : 'gd alt password');

defined('EX_GD_ALT_PROJECT') || define('EX_GD_ALT_PROJECT', getenv('EX_GD_ALT_PROJECT')
    ? getenv('EX_GD_ALT_PROJECT') : 'gd alt project');

defined('EX_GD_ALT_HOST') || define('EX_GD_ALT_HOST', getenv('EX_GD_ALT_HOST')
    ? getenv('EX_GD_ALT_HOST') : 'gd alt host');

defined('GD_PROVISIONING_PID') || define('GD_PROVISIONING_PID', getenv('GD_PROVISIONING_PID')
    ? getenv('GD_PROVISIONING_PID') : 'gd provisioning pid');

defined('GD_PROVISIONING_URL') || define('GD_PROVISIONING_URL', getenv('GD_PROVISIONING_URL')
    ? getenv('GD_PROVISIONING_URL') : 'gd provisioning url');

defined('KBC_TOKEN') || define('KBC_TOKEN', getenv('KBC_TOKEN')
    ? getenv('KBC_TOKEN') : 'kbc token');

defined('KBC_URL') || define('KBC_URL', getenv('KBC_URL')
    ? getenv('KBC_URL') : 'kbc url');

require_once __DIR__ . '/../vendor/autoload.php';
