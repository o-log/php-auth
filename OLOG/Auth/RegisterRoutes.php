<?php

namespace OLOG\Auth;

use OLOG\Router;

class RegisterRoutes
{
    static public function registerRoutes(){
        Router::matchAction(\OLOG\Auth\Admin\UsersListAction::class, 0);
        Router::matchAction(\OLOG\Auth\Admin\UserEditAction::class, 0);

        Router::matchAction(\OLOG\Auth\Pages\LoginAction::class, 0);
    }
}