## Подключение модуля

Подключить модуль в composer.json

    "require": {
      "o-log/php-auth": "2.*"
    }

Выполнить composer update

Регистрируем роутинг для админки и страниц регистрации и авторизации: добавить в точку входа сайта строку

    \OLOG\Auth\RegisterRoutes::registerRoutes();

Добавляем в конфиг сайта базу данных с идентификатором DB_NAME_PHPAUTH и указать для нее файл sql ...

    DBConfig::setDBSettingsObj(
      AuthConstants::DB_NAME_PHPAUTH,
      new DBSettings('localhost', 'db_projectname', 'root', '1', 'vendor/o-log/php-auth/db_phpauth.sql')
    );

5. Выполнить cli.php в корне сайта чтобы создать таблицы для пользователей и записи пермишенов

После этого админка авторизации должна заработать по адресу /admin/auth

Изначально она будет недоступна, потому что нет ни одного пользователя, который имел бы к ней доступ.
Чтобы создать такого пользователя выполняем команду:

    php bin/pa_makeuser.php username password

Замечание: сейчас для хранения сессий используется только мемкеш, поэтому на компе должен быть рабочий мемкеш и в конфиге должна быть настройка такого вида:

    CacheConfig::addServerSettingsObj(new MemcacheServerSettings('localhost', 11211));

## Создание нового разрешения в другом модуле

Создаем константу в классе Модуль/Permissions (название класса можно использовать любое, это просто удобное соглашение)

    <?php

    namespace MODULENAME;

    class Permissions
    {
        const PERMISSION_MODULENAME_ACCESS_ADMIN = 'PERMISSION_MODULENAME_ACCESS_ADMIN';
    }

Имя константы примерно такое: PERMISSION_MODULENAME_MANAGE_NODES где MODULENAME - имя вашего модуля, а MANAGE_NODES - пример названия собственно разрешения.

Эта константа нужна для использования в коде для проверки разрешений.

Имя разрешения надо вставить в таблицу разрешений: руками добавить sql-запрос в файл sql-запросов модуля. Вот пример:

    'insert into olog_auth_permission (title) values ("PERMISSION_MODULENAME_MANAGE_NODES") /* 364563456 */;',

Запись в таблице будет использоваться админкой для назначения разрешений операторам.

## Инструкция по добавлению владельца к модели

Добавить поля владельцев с внешними ключами
- owner_user_id int nullable
- owner_group_id int nullable

Добавить в implements InterfaceOwner

Добавить в beforeSave модели инициализацию полей владельцев из текущего пользователя и его основной группы:

    public function beforeSave()
    {
        OwnerAssign::assignCurrentUserAsOwnerToObj($this);
    }


Добавить фильтры к спискам: CRUDTableFilterOwner

Добавить проверку прав в редакторы: OwnerCheck::currentUserOwnsObj()

Если нужно - проставить владельцев для существующих моделей
