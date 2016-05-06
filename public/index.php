<?php

require_once '../vendor/autoload.php';

\OLOG\ConfWrapper::assignConfig(\Config\Config::get());

//
// Роуты
//

\OLOG\Auth\RegisterRoutes::registerRoutes();

\OLOG\Router::matchAction(\PhpAuthDemo\Pages\MainPageAction::class);


//
// Обработка после завершения роутинга
//

// support for local php server (php -S) - tells local server to return static files
if (\OLOG\ConfWrapper::value('return_false_if_no_route', false)) {
    return false;
}