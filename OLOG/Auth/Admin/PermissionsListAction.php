<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\Permissions;
use OLOG\Exits;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;

class PermissionsListAction extends AuthAdminActionsBaseProxy implements
    InterfaceAction,
    InterfacePageTitle
{
    public function pageTitle()
    {
        return 'Разрешения';
    }

    public function url()
    {
        return '/admin/auth/permissions';
    }

    public function action()
    {
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS])
        );

        $html = \OLOG\CRUD\CRUDTable::html(
            \OLOG\Auth\Permission::class,
            null,
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    '', new \OLOG\CRUD\CRUDTableWidgetText('{this->id}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    '', new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->title}', (new \OLOG\Auth\Admin\PermissionToUserListAction('{this->id}'))->url())
                ),
            ],
            [],
            'title'
        );

        AdminLayoutSelector::render($html, $this);
    }
}