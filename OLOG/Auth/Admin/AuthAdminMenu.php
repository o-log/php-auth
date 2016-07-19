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
                new MenuItem(UsersListAction::pageTitle(), UsersListAction::getUrl()),
                new MenuItem(OperatorsListAction::pageTitle(), OperatorsListAction::getUrl(), NULL, 'glyphicon glyphicon-user'),
                new MenuItem(PermissionsListAction::pageTitle(), PermissionsListAction::getUrl()),
            ], 'glyphicon glyphicon-user')
        ];
    }

}