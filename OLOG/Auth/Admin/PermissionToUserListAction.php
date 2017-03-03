<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\Permission;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\Exits;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;

class PermissionToUserListAction extends AuthAdminActionsBaseProxy implements
    InterfaceAction,
    InterfacePageTitle
{
    protected $permission_id;

    public function topActionObj()
    {
        return new PermissionsListAction();
    }

    public function __construct($permission_id)
    {
        $this->permission_id = $permission_id;
    }

    public function pageTitle()
    {
        return 'Пользователи разрешения';
    }

    public function url()
    {
        return '/admin/auth/permission_to_user/' . $this->permission_id;
    }

    public function urlMask()
    {
        return '/admin/auth/permission_to_user/(\d+)';
    }

    public function action()
    {
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS])
        );

        $permission_obj = Permission::factory($this->permission_id);
        $html = \OLOG\HTML::tag('h3', [], $permission_obj->getTitle());
        $html .= \OLOG\CRUD\CRUDTable::html(
            \OLOG\Auth\PermissionToUser::class,
            null,
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    '', new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{' . User::class . '.{this->user_id}->login}', (new UserEditAction('{this->user_id}'))->url())
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    '', new \OLOG\CRUD\CRUDTableWidgetDelete()
                )
            ],
            [
                new CRUDTableFilterEqualInvisible('permission_id', $this->permission_id)
            ]
        );

        AdminLayoutSelector::render($html, $this);
    }
}