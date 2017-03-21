<?php

namespace OLOG\Auth\Logger\Admin;

use OLOG\CheckClassInterfaces;
use OLOG\Layouts\InterfaceCurrentUserName;
use OLOG\Layouts\InterfaceMenu;
use OLOG\Layouts\InterfaceSiteTitle;
use OLOG\Layouts\InterfaceTopActionObj;
use OLOG\Auth\Logger\LoggerConfig;

class LoggerAdminActionsBaseProxy implements
    InterfaceMenu,
    InterfaceTopActionObj,
    InterfaceSiteTitle,
    InterfaceCurrentUserName
{
    static public function menuArr(){
        $admin_actions_base_classname = LoggerConfig::getAdminActionsBaseClassname();
        if (CheckClassInterfaces::classImplementsInterface($admin_actions_base_classname, InterfaceMenu::class)){
            return $admin_actions_base_classname::menuArr();
        }
        return [];
    }

    public function topActionObj(){
        $admin_actions_base_classname = LoggerConfig::getAdminActionsBaseClassname();
        if (CheckClassInterfaces::classImplementsInterface($admin_actions_base_classname, InterfaceTopActionObj::class)){
            return (new $admin_actions_base_classname())->topActionObj();
        }
        return null;
    }

    public function siteTitle(){
        $admin_actions_base_classname = LoggerConfig::getAdminActionsBaseClassname();
        if (CheckClassInterfaces::classImplementsInterface($admin_actions_base_classname, InterfaceSiteTitle::class)){
            return (new $admin_actions_base_classname())->siteTitle();
        }
        return '';
    }

    public function currentUserName(){
        $admin_actions_base_classname = LoggerConfig::getAdminActionsBaseClassname();
        if (CheckClassInterfaces::classImplementsInterface($admin_actions_base_classname, InterfaceCurrentUserName::class)){
            return (new $admin_actions_base_classname())->currentUserName();
        }
        return '';
    }
}
