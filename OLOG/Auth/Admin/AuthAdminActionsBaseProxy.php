<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\AuthConfig;
use OLOG\Layouts\CurrentUserNameInterface;
use OLOG\Layouts\MenuInterface;
use OLOG\Layouts\SiteTitleInterface;
use OLOG\Layouts\TopActionObjInterface;

class AuthAdminActionsBaseProxy implements
    MenuInterface,
    TopActionObjInterface,
    SiteTitleInterface,
    CurrentUserNameInterface
{
    static public function menuArr(){
        $admin_actions_base_classname = AuthConfig::getAdminActionsBaseClassname();
        if (is_a($admin_actions_base_classname, MenuInterface::class, true)){
            return $admin_actions_base_classname::menuArr();
        }

        return [];
    }

    public function topActionObj(){
        $admin_actions_base_classname = AuthConfig::getAdminActionsBaseClassname();
        if (is_a($admin_actions_base_classname, TopActionObjInterface::class, true)){
            return (new $admin_actions_base_classname())->topActionObj();
        }

        return null;
    }

    public function siteTitle(){
        $admin_actions_base_classname = AuthConfig::getAdminActionsBaseClassname();
        if (is_a($admin_actions_base_classname, SiteTitleInterface::class, true)){
            return (new $admin_actions_base_classname())->siteTitle();
        }

        return '';
    }

    public function currentUserName(){
        $admin_actions_base_classname = AuthConfig::getAdminActionsBaseClassname();
        if (is_a($admin_actions_base_classname, CurrentUserNameInterface::class, true)){
            return (new $admin_actions_base_classname())->currentUserName();
        }

        return '';
    }
}