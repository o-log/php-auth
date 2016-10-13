<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Permissions;
use OLOG\Layouts\InterfaceMenu;
use OLOG\Layouts\MenuItem;

class AuthAdminMenu implements InterfaceMenu
{
    static public function menuArr()
    {
        return [
            new MenuItem('Авторизация', '', [
                new MenuItem(UsersListAction::pageTitle(), UsersListAction::getUrl(), NULL, 'glyphicon glyphicon-user', [Permissions::PERMISSION_PHPAUTH_MANAGE_USERS]),
                new MenuItem(OperatorsListAction::pageTitle(), OperatorsListAction::getUrl(), NULL, 'glyphicon glyphicon-eye-open', [Permissions::PERMISSION_PHPAUTH_MANAGE_OPERATORS]),
                new MenuItem(PermissionsListAction::pageTitle(), PermissionsListAction::getUrl(), NULL, 'glyphicon glyphicon-check', [Permissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS]),
                new MenuItem(GroupsListAction::pageTitle(), GroupsListAction::getUrl(), NULL, 'glyphicon glyphicon-check', [Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS]),
            ], 'glyphicon glyphicon-log-in', [Permissions::PERMISSION_PHPAUTH_MANAGE_USERS, Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS, Permissions::PERMISSION_PHPAUTH_MANAGE_OPERATORS])
        ];
    }

}