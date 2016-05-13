<?php

namespace Config;

use OLOG\Auth\Constants;

class Config
{
    public static function get()
    {
        $conf['cache_lifetime'] = 60;
        $conf['return_false_if_no_route'] = true; // for local php server
        $conf['db'] = array(
            Constants::DB_NAME_PHPAUTH => array(
                'host' => 'localhost',
                'db_name' => 'db_phpauthdemo',
                'user' => 'root',
                'pass' => '1'
            )
        );

        $conf['php-bt'] = [
            'layout_code' => \OLOG\BT\LayoutGentellela::LAYOUT_CODE_GENTELLELA,
            'menu_classes_arr' => [
                \PhpAuthDemo\AuthDemoMenu::class
            ]
        ];
        
        return $conf;
    }
}