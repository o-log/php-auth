<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\Permissions;
use OLOG\Exits;
use OLOG\HTML;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;

class AuthAdminAction extends AuthAdminActionsBaseProxy implements
    InterfaceAction,
    InterfacePageTitle
{
    use CurrentUserNameTrait;

    public function url()
    {
        return '/admin/auth';
    }

    public function pageTitle()
    {
        return 'Авторизация';
    }

    public function action()
    {
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions(
                [
                    Permissions::PERMISSION_PHPAUTH_MANAGE_OPERATORS,
                    Permissions::PERMISSION_PHPAUTH_MANAGE_USERS
                ]
            )
        );

        $html = '';
        $html .= '<div>' . HTML::a((new PermissionsListAction())->url(), 'Разрешения') . '</div>';
        $html .= '<div>' . HTML::a((new UsersListAction())->url(), 'Пользователи') . '</div>';
        $html .= '<div>' . HTML::a((new GroupsListAction())->url(), 'Группы') . '</div>';
        $html .= '<div>' . HTML::a((new OperatorsListAction())->url(), 'Операторы') . '</div>';

        AdminLayoutSelector::render($html, $this);
    }
}