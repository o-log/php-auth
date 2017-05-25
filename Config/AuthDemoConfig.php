<?php

namespace Config;

use OLOG\Auth\AuthConfig;
use OLOG\Auth\AuthConstants;
use OLOG\BT\LayoutBootstrap;
use OLOG\Cache\CacheConfig;
use OLOG\Cache\CacheRedis;
use OLOG\Cache\CacheServerSettings;
use OLOG\Cache\MemcacheServerSettings;
use OLOG\DB\DBConfig;
use OLOG\DB\DBSettings;
use OLOG\Layouts\LayoutsConfig;
use PhpAuthDemo\AdminDemoActionsBase;
use PhpAuthDemo\UserEvents;

class AuthDemoConfig
{
   public static function init()
    {
        date_default_timezone_set('Europe/Moscow');

        DBConfig::setDBSettingsObj(
            AuthConstants::DB_NAME_PHPAUTH,
            new DBSettings('localhost', 'db_phpauthdemo', 'root', '1')
        );

        CacheConfig::setEngineClassname(
            CacheRedis::class
        );

        CacheConfig::addServerSettingsObj(
            new CacheServerSettings('localhost', 6379)
        );

        AuthConfig::setExtraCookiesArr(
          [
              'ignore_nginx_cache' => 1
          ]

        );

        AuthConfig::setAdminActionsBaseClassname(AdminDemoActionsBase::class);
        LayoutsConfig::setAdminLayoutClassName(LayoutBootstrap::class);

        //AuthConfig::setDefaultRedirectUrlAfterSuccessfulLogin('/admin/auth/users');

        //AuthConfig::setUserEventClass(UserEvents::class);

		//AuthConfig::setFullAccessCookieName('php_auth');
    }
}