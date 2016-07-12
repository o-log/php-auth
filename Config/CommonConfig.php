<?php

namespace Config;

use OLOG\Auth\Constants;
use OLOG\Model\ModelConstants;

class CommonConfig
{
    public static function get()
    {
        date_default_timezone_set('Europe/Moscow');

        $conf = [];
        
        $conf['cache_lifetime'] = 60;
        $conf['return_false_if_no_route'] = true; // for local php server

        $conf[ModelConstants::MODULE_CONFIG_ROOT_KEY] = [
            'db' => [
                Constants::DB_NAME_PHPAUTH => [
                    'host' => 'localhost',
                    'db_name' => 'db_phpauthdemo',
                    'user' => 'root',
                    'pass' => '1'
                ]
            ]
        ];

        $conf['memcache_servers'] = [
            'localhost:11211'
        ];

        $conf['php-bt'] = [
            'menu_classes_arr' => [
                \OLOG\Auth\Admin\AuthAdminMenu::class
            ],
            'application_title' => 'Auth demo'
        ];

        $conf['php_auth'] = [
            'full_access_cookie_name' => 'jkhbsdfhjvkdfvjgvasdc'
        ];
        
        return $conf;
    }
}