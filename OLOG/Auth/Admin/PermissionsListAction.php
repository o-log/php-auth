<?php

namespace OLOG\Auth\Admin;

use OLOG\ActionInterface;
use OLOG\Auth\Auth;
use OLOG\Auth\Permission;
use OLOG\Auth\Permissions;
use OLOG\CRUD\CTable;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TWText;
use OLOG\CRUD\TWTextWithLink;
use OLOG\Exits;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\PageTitleInterface;

class PermissionsListAction extends AuthAdminActionsBaseProxy implements
    ActionInterface,
    PageTitleInterface
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
        /*
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS])
        );
        */
        Auth::check([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS]);

        $html = CTable::html(
            Permission::class,
            null,
            [
                new TCol(
                    '', new TWText('{this->id}')
                ),
                new TCol(
                    '', new TWTextWithLink('{this->title}', (new PermissionToUserListAction('{this->id}'))->url())
                ),
            ],
            [],
            'title'
        );

        AdminLayoutSelector::render($html, $this);
    }
}