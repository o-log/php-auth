<?php

namespace PhpAuthDemo;

use OLOG\Auth\Admin\OperatorsListAction;
use OLOG\Auth\Admin\PermissionsListAction;
use OLOG\Auth\Admin\UsersListAction;
use OLOG\BT\InterfaceMenu;
use OLOG\BT\MenuItem;

class AuthDemoMenu implements InterfaceMenu
{
    static public function menuArr()
    {
        return [
            new MenuItem('Auth', '', [
                new MenuItem(UsersListAction::pageTitle(), UsersListAction::getUrl()),
                new MenuItem(OperatorsListAction::pageTitle(), OperatorsListAction::getUrl()),
                new MenuItem(PermissionsListAction::pageTitle(), PermissionsListAction::getUrl()),
            ])
        ];
    }

}