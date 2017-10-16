<?php

namespace PhpAuthDemo;

use OLOG\Auth\Admin\AuthAdminMenu;
use OLOG\Layouts\MenuInterface;

class AdminDemoActionsBase implements MenuInterface
{
    static public function menuArr()
    {
        $menu_arr = [];

        $menu_arr = array_merge($menu_arr, AuthAdminMenu::menuArr());

        return $menu_arr;
    }

}