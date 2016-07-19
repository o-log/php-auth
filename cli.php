<?php

require_once 'vendor/autoload.php';

\Config\AuthDemoConfig::init();

\OLOG\Model\CLI\CLIMenu::run();