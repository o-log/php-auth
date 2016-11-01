<?php


namespace OLOG\Auth\Admin;


use OLOG\Auth\CRUDTableFilterOwnerInvisible;
use OLOG\Auth\Operator;
use OLOG\Auth\Permissions;
use OLOG\Exits;
use OLOG\InterfaceAction;

class GroupsListAjaxAction implements InterfaceAction
{
    public function url(){
        return '/admin/auth/groups_ajax';
    }

    public function action(){
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS])
        );

        $html = \OLOG\CRUD\CRUDTable::html(
            \OLOG\Auth\Group::class,
            '',
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    '',
                    new \OLOG\CRUD\CRUDTableWidgetReferenceSelect(\OLOG\Auth\Group::_TITLE)
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Название',
                    new \OLOG\CRUD\CRUDTableWidgetText('{this->'.\OLOG\Auth\Group::_TITLE.'}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Создана',
                    new \OLOG\CRUD\CRUDTableWidgetTimestamp('{this->'.\OLOG\Auth\Group::_CREATED_AT_TS.'}')
                )
            ],
            [
                new CRUDTableFilterOwnerInvisible()
            ]
        );

        echo $html;
    }
}