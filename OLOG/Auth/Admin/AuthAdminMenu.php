<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth\Admin;

use OLOG\Auth\Auth;
use OLOG\Auth\AuthPermissions;
use OLOG\Layouts\MenuInterface;
use OLOG\Layouts\MenuItem;

class AuthAdminMenu implements MenuInterface
{
    static public function menuArr()
    {
        $menu_arr = [];

        if (Auth::currentUserHasAnyOfPermissions([AuthPermissions::PERMISSION_PHPAUTH_MANAGE_USERS, AuthPermissions::PERMISSION_PHPAUTH_MANAGE_GROUPS])) {
            $menu_arr = [
                new MenuItem('Пользователи', '', [
                    new MenuItem((new UsersListAction())->pageTitle(), (new UsersListAction())->url(), [], 'fa fa-user'),
                    //new MenuItem((new OperatorsListAction())->pageTitle(), (new OperatorsListAction())->url(), [], 'fa fa-eye-open'),
                    new MenuItem((new PermissionsListAction())->pageTitle(), (new PermissionsListAction())->url(), [], 'fa fa-eye'),
                    new MenuItem((new GroupsListAction())->pageTitle(), (new GroupsListAction())->url(), [], 'fa fa-check'),
                ], 'fa fa-user')
            ];
        }

        return $menu_arr;
    }

}
