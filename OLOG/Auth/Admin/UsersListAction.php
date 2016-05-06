<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\User;
use OLOG\BT\Layout;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;

class UsersListAction
{
    static public function getUrl(){
        return '/admin/users';
    }

    public function action(){

        // TODO: check permissions

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