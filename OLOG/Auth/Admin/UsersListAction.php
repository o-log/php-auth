<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\Permission;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\BT\Layout;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\Exits;

class UsersListAction
{
    static public function getUrl(){
        return '/admin/users';
    }

    public function action(){
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS])
        );

        $html = \OLOG\CRUD\CRUDTable::html(
            \OLOG\Auth\User::class,
            CRUDForm::html(
                new User(),
                [
                    new CRUDFormRow('login', new CRUDFormWidgetInput('login'))
                ]
            ),
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    'ID', new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->id}', UserEditAction::getUrl('{this->id}'))
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'login', new \OLOG\CRUD\CRUDTableWidgetText('{this->login}')
                )
            ]
        );

        Layout::render($html, $this);
    }
}