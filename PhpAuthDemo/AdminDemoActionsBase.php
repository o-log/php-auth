<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace PhpAuthDemo;

use OLOG\Auth\Admin\AuthAdminMenu;
use OLOG\Auth\Admin\CurrentUserNameTrait;
use OLOG\BT\LayoutBootstrap4;
use OLOG\Layouts\CurrentUserNameInterface;
use OLOG\Layouts\MenuInterface;
use OLOG\Layouts\RenderInLayoutInterface;

class AdminDemoActionsBase
    implements MenuInterface, CurrentUserNameInterface, RenderInLayoutInterface
{
    use CurrentUserNameTrait;

    static public function menuArr()
    {
        $menu_arr = [];

        $menu_arr = array_merge($menu_arr, AuthAdminMenu::menuArr());

        return $menu_arr;
    }

    public function renderInLayout($html_or_callable)
    {
        LayoutBootstrap4::render($html_or_callable, $this);
    }
}
