<?php

namespace OLOG\Auth\Logger\Admin;

use OLOG\Auth\Auth;
use OLOG\Layouts\InterfaceMenu;
use OLOG\Layouts\MenuItem;
use OLOG\Auth\Logger\Permissions;

class LoggerAdminMenu implements InterfaceMenu
{
    static public function menuArr()
    {
        $menu_arr =  [];
        if (Auth::currentUserHasAnyOfPermissions([Permissions::PERMISSION_PHPLOGGER_ACCESS])) {
            $menu_arr = [
                new MenuItem((new EntriesListAction())->pageTitle(), (new EntriesListAction())->url(), [], 'glyphicon glyphicon-flag')
            ];
        }
        return $menu_arr;
    }

}