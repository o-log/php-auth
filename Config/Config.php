<?php

namespace Config;

use OLOG\Auth\AuthConfig;
use OLOG\BT\LayoutBootstrap;
use OLOG\BT\LayoutBootstrap4;
use OLOG\Cache\BucketMemcache;
use OLOG\Cache\CacheConfig;
use OLOG\Cache\MemcacheServer;
use OLOG\DB\ConnectorMySQL;
use OLOG\DB\DBConfig;
use OLOG\DB\Space;
use OLOG\Layouts\LayoutsConfig;
use PhpAuthDemo\AdminDemoActionsBase;

class Config
{
    const CONNECTOR_AUTH = 'CONNECTOR_AUTH';

    public static function init()
    {
        date_default_timezone_set('Europe/Moscow');
        ini_set('assert.exception', true);

        DBConfig::setConnector(
            self::CONNECTOR_AUTH,
            new ConnectorMySQL('127.0.0.1', 'db_phpauthdemo', 'root', '1')
        );

        DBConfig::setSpace(
            AuthConfig::SPACE_PHPAUTH,
            new Space(self::CONNECTOR_AUTH, 'db_phpauth.sql')
        );

        CacheConfig::setBucket(
            '',
            new BucketMemcache([new MemcacheServer('127.0.0.1', 11211)])
        );

        AuthConfig::setExtraCookiesArr(
            [
                'ignore_nginx_cache' => 1
            ]
        );

        AuthConfig::setAdminActionsBaseClassname(AdminDemoActionsBase::class);
        LayoutsConfig::setAdminLayoutClassName(LayoutBootstrap4::class);

        AuthConfig::setDefaultRedirectUrlAfterSuccessfulLogin('/admin/auth/users');

        AuthConfig::setLoginUrl('/login');
        AuthConfig::setLogoutUrl('/logout');
    }
}
