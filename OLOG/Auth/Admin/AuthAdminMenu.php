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
                new MenuItem('Авторизация', '', [
                    new MenuItem(UsersListAction::pageTitle(), UsersListAction::getUrl(), NULL, 'glyphicon glyphicon-user'),
                    new MenuItem(OperatorsListAction::pageTitle(), OperatorsListAction::getUrl(), NULL, 'glyphicon glyphicon-eye-open'),
                    new MenuItem(PermissionsListAction::pageTitle(), PermissionsListAction::getUrl(), NULL, 'glyphicon glyphicon-check'),
                    new MenuItem(GroupsListAction::pageTitle(), GroupsListAction::getUrl(), NULL, 'glyphicon glyphicon-check'),
                ], 'glyphicon glyphicon-log-in')
            ];
        }

        return $menu_arr;
    }

}