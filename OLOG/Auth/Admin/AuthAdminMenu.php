<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\Permissions;
use OLOG\Layouts\InterfaceMenu;
use OLOG\Layouts\MenuItem;

class AuthAdminMenu implements InterfaceMenu
{
    static public function menuArr()
    {
        $menu_arr = [];

        if (Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS, Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS, Permissions::PERMISSION_PHPAUTH_MANAGE_OPERATORS])) {
            $menu_arr = [
                new MenuItem('Пользователи', '', [
                    new MenuItem((new UsersListAction())->pageTitle(), (new UsersListAction())->url(), [], 'glyphicon glyphicon-user'),
                    new MenuItem((new OperatorsListAction())->pageTitle(), (new OperatorsListAction())->url(), [], 'glyphicon glyphicon-eye-open'),
                    new MenuItem((new PermissionsListAction())->pageTitle(), (new PermissionsListAction())->url(), [], 'glyphicon glyphicon-check'),
                    new MenuItem((new GroupsListAction())->pageTitle(), (new GroupsListAction())->url(), [], 'glyphicon glyphicon-check'),
                ], 'glyphicon glyphicon-log-in')
            ];
        }

        return $menu_arr;
    }

}