<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\Permission;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\BT\BT;
use OLOG\BT\InterfaceBreadcrumbs;
use OLOG\BT\InterfacePageTitle;
use OLOG\BT\InterfaceUserName;
use OLOG\BT\Layout;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDTableFilter;
use OLOG\Exits;

class UsersListAjaxAction
{
    use CurrentUserNameTrait;

    static public function getUrl(){
        return '/admin/auth/users_ajax';
    }

    public function action(){
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS])
        );

        $html = \OLOG\CRUD\CRUDTable::html(
            \OLOG\Auth\User::class,
            '',
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    'Логин',
                    new \OLOG\CRUD\CRUDTableWidgetReferenceSelect('login')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Логин',
                    new \OLOG\CRUD\CRUDTableWidgetText('{this->login}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Создан',
                    new \OLOG\CRUD\CRUDTableWidgetText('{this->created_at_ts}')
                )
            ],
            [
                new CRUDTableFilter('login', CRUDTableFilter::FILTER_LIKE, '')
            ]
        );

        echo $html;
    }
}