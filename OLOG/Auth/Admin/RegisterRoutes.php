<?php

namespace OLOG\Auth\Admin;

use OLOG\Router;

class RegisterRoutes
{
    static public function registerRoutes(){
        Router::matchAction(\OLOG\Auth\Admin\UsersListAction::class);
        Router::matchAction(\OLOG\Auth\Admin\UserEditAction::class);
    }
}