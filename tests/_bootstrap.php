<?php
// This is global bootstrap for autoloading
echo sprintf("Running SOCIAL tests in: %s\n\r", __DIR__);

// This is global bootstrap for autoloading

require(__DIR__ . '/../../../autoload.php');
require(__DIR__ . '/../../../yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/console.php');
//
$application = new yii\console\Application( $config );