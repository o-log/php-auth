<?php

require_once '../vendor/autoload.php';

\Config\AuthDemoConfig::init();

//
// Роуты
//

\OLOG\Auth\RegisterRoutes::registerRoutes();
\OLOG\Auth\Logger\RegisterRoutes::registerRoutes();

\OLOG\Router::processAction(\PhpAuthDemo\Pages\MainPageAction::class, 0);

//
// Обработка после завершения роутинга
//

// support for local php server (php -S) - tells local server to return static files
/*
if (\OLOG\ConfWrapper::value('return_false_if_no_route', false)) {
    return false;
}
*/