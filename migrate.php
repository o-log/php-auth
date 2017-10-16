<?php

require_once 'vendor/autoload.php';

\Config\AuthDemoConfig::init();

\OLOG\DB\MigrateCLI::run();