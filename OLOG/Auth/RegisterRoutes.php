<?php

namespace OLOG\Auth;

use OLOG\Auth\Admin\AuthAdminAction;
use OLOG\Router;

class RegisterRoutes
{
    static public function registerRoutes(){
        Router::matchAction(AuthAdminAction::class, 0);
        Router::matchAction(\OLOG\Auth\Admin\UsersListAction::class, 0);
        Router::matchAction(\OLOG\Auth\Admin\UserEditAction::class, 0);
        Router::matchAction(\OLOG\Auth\Admin\PermissionsListAction::class, 0);

        Router::matchAction(\OLOG\Auth\Pages\LoginAction::class, 0);
    }
}