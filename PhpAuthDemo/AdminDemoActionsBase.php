<?php

namespace PhpAuthDemo;

use OLOG\Auth\Admin\AuthAdminMenu;
use OLOG\Layouts\InterfaceMenu;
use OLOG\Logger\Admin\LoggerAdminMenu;

class AdminDemoActionsBase implements InterfaceMenu
{
    static public function menuArr()
    {
        $menu_arr = [];

        $menu_arr = array_merge($menu_arr, AuthAdminMenu::menuArr());
        $menu_arr = array_merge($menu_arr, LoggerAdminMenu::menuArr());

        return $menu_arr;
    }

}