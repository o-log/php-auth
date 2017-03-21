<?php

namespace OLOG\Auth\Logger;

use OLOG\Auth\Logger\Admin\EntriesListAction;
use OLOG\Auth\Logger\Admin\EntryEditAction;
use OLOG\Auth\Logger\Admin\ObjectEntriesListAction;
use OLOG\Router;

class RegisterRoutes
{
    static public function registerRoutes(){
        Router::processAction(EntriesListAction::class, 0);
        Router::processAction(EntryEditAction::class, 0);
        Router::processAction(ObjectEntriesListAction::class, 0);
    }
}