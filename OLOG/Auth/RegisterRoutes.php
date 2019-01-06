<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth;

use OLOG\Auth\Admin\AuthAdminAction;
use OLOG\Auth\Admin\GroupEditAction;
use OLOG\Auth\Admin\GroupsListAction;
use OLOG\Auth\Admin\GroupsListAjaxAction;
use OLOG\Auth\Admin\PermissionAddToUserAction;
use OLOG\Auth\Admin\PermissionsListAction;
use OLOG\Auth\Admin\PermissionToUserListAction;
use OLOG\Auth\Admin\UserEditAction;
use OLOG\Auth\Admin\UsersListAction;
use OLOG\Auth\Admin\UsersListAjaxAction;
use OLOG\Auth\Pages\LoginAction;
use OLOG\Auth\Pages\LogoutAction;
use OLOG\Router;

class RegisterRoutes
{
    static public function registerRoutes(){
        Router::action(AuthAdminAction::class, 0);
        Router::action(UsersListAction::class, 0);
        Router::action(UserEditAction::class, 0);
        Router::action(PermissionsListAction::class, 0);
        Router::action(PermissionToUserListAction::class, 0);
        Router::action(UsersListAjaxAction::class, 0);
        Router::action(GroupsListAction::class, 0);
        Router::action(GroupEditAction::class, 0);
        Router::action(GroupsListAjaxAction::class, 0);

        Router::action(PermissionAddToUserAction::class, 0);

        Router::action(LoginAction::class, 0);
        Router::action(LogoutAction::class, 0);
    }
}
