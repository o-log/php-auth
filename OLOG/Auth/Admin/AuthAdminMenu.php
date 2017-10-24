<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Auth;
use OLOG\Auth\Permissions;
use OLOG\Layouts\MenuInterface;
use OLOG\Layouts\MenuItem;

class AuthAdminMenu implements MenuInterface
{
    static public function menuArr()
    {
        $menu_arr = [];

        if (Auth::currentUserHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS, Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS])) {
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
