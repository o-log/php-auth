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
    const DBCONNECTOR_ID_AUTH = 'DBCONNECTOR_ID_AUTH';

    public static function init()
    {
        date_default_timezone_set('Europe/Moscow');

        \OLOG\DB\DBConfig::setDBConnectorObj(
            self::DBCONNECTOR_ID_AUTH,
            new \OLOG\DB\DBConnector('localhost', 'db_phpauthdemo', 'root', '1')
        );

        DBConfig::setDBSettingsObj(
            LoggerConstants::DB_NAME_PHPLOGGER,
            new DBSettings('', '', '', '', 'vendor/o-log/php-logger/' . LoggerConstants::DB_NAME_PHPLOGGER . '.sql', self::DBCONNECTOR_ID_AUTH)
        );

        DBConfig::setDBSettingsObj(
            AuthConstants::DB_NAME_PHPAUTH,
            new DBSettings('', '', '', '', 'db_phpauth.sql', self::DBCONNECTOR_ID_AUTH)
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
    }
}