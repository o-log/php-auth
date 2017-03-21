<?php

namespace OLOG\Auth\Logger;

class LoggerConfig
{
    static protected $admin_actions_base_classname;

    /**
     * @return mixed
     */
    public static function getAdminActionsBaseClassname()
    {
        return self::$admin_actions_base_classname;
    }

    /**
     * @param mixed $admin_actions_base_classname
     */
    public static function setAdminActionsBaseClassname($admin_actions_base_classname)
    {
        self::$admin_actions_base_classname = $admin_actions_base_classname;
    }
}