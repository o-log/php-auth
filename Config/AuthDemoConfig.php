<?php

namespace Config;

use OLOG\Auth\AuthConfig;
use OLOG\Auth\AuthConstants;
use OLOG\BT\LayoutBootstrap;
use OLOG\Cache\CacheConfig;
use OLOG\Cache\MemcacheServerSettings;
use OLOG\DB\DBConfig;
use OLOG\DB\DBSettings;
use OLOG\Layouts\LayoutsConfig;
use OLOG\Logger\LoggerConfig;
use OLOG\Logger\LoggerConstants;
use PhpAuthDemo\AdminDemoActionsBase;
use PhpAuthDemo\UserEvents;

class AuthDemoConfig
{
   public static function init()
    {
        date_default_timezone_set('Europe/Moscow');

        \OLOG\DB\DBConfig::setDBConnectorObj(
            \OLOG\Auth\AuthConfig::DBCONNECTOR_ID_AUTH,
            new \OLOG\DB\DBConnector('localhost', 'db_phpauthdemo', 'root', '1')
        );

        DBConfig::setDBSettingsObj(
            LoggerConstants::DB_NAME_PHPLOGGER,
            new DBSettings('', '', '', '', 'vendor/o-log/php-logger/' . LoggerConstants::DB_NAME_PHPLOGGER . '.sql', \OLOG\Auth\AuthConfig::DBCONNECTOR_ID_AUTH)
        );

        DBConfig::setDBSettingsObj(
            AuthConstants::DB_NAME_PHPAUTH,
            new DBSettings('', '', '', '', 'db_phpauth.sql', \OLOG\Auth\AuthConfig::DBCONNECTOR_ID_AUTH)
        );

        CacheConfig::addServerSettingsObj(
            new MemcacheServerSettings('localhost', 11211)
        );

        AuthConfig::setExtraCookiesArr(
          [
              'ignore_nginx_cache' => 1
          ]

        );

        AuthConfig::setAdminActionsBaseClassname(AdminDemoActionsBase::class);
        LoggerConfig::setAdminActionsBaseClassname(AdminDemoActionsBase::class);
        LayoutsConfig::setAdminLayoutClassName(LayoutBootstrap::class);

       // AuthConfig::setUserEventClass(UserEvents::class);

		//AuthConfig::setFullAccessCookieName('php_auth');

        /*
        $conf['return_false_if_no_route'] = true; // for local php server

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
        */
    }
}