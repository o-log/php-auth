<?php

require_once 'vendor/autoload.php';

\OLOG\ConfWrapper::assignConfig(\Config\Config::get());

\OLOG\Model\CLI\CLIMenu::run();