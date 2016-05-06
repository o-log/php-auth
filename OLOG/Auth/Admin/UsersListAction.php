<?php

class UsersListAction
{
    static public function getUrl(){
        return '/admin/users';
    }

    public function action(){
        $html = \OLOG\CRUD\CRUDTable::html(
            \OLOG\Auth\User::class,
            '',
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    'ID', new \OLOG\CRUD\CRUDTableWidgetText('{this->id}')
                )
            ]
        );

        echo $html;
    }
}