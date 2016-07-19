<?php

namespace OLOG\Auth\Admin;

use OLOG\BT\InterfaceMenu;
use OLOG\BT\MenuItem;

class AuthAdminMenu implements InterfaceMenu
{
    static public function menuArr()
    {
        return [
            new MenuItem('Авторизация', '', [
                new MenuItem(UsersListAction::pageTitle(), UsersListAction::getUrl(), NULL, 'glyphicon glyphicon-user'),
                new MenuItem(OperatorsListAction::pageTitle(), OperatorsListAction::getUrl(), NULL, 'glyphicon glyphicon-eye-open'),
                new MenuItem(PermissionsListAction::pageTitle(), PermissionsListAction::getUrl(), NULL, 'glyphicon glyphicon-check'),
            ], 'glyphicon glyphicon-log-in')
        ];
    }

}