<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth\Admin;

use OLOG\ActionInterface;
use OLOG\Auth\Auth;
use OLOG\Auth\Permissions;
use OLOG\HTML;
use OLOG\Layouts\PageTitleInterface;

class AuthAdminAction
    extends AuthAdminActionsBaseProxy
    implements ActionInterface, PageTitleInterface
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
        /*
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions(
                [
                    Permissions::PERMISSION_PHPAUTH_MANAGE_OPERATORS,
                    Permissions::PERMISSION_PHPAUTH_MANAGE_USERS
                ]
            )
        );
        */
        Auth::check([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS]);

        $html = '';
        $html .= '<div>' . HTML::a((new PermissionsListAction())->url(), 'Разрешения') . '</div>';
        $html .= '<div>' . HTML::a((new UsersListAction())->url(), 'Пользователи') . '</div>';
        $html .= '<div>' . HTML::a((new GroupsListAction())->url(), 'Группы') . '</div>';

        $this->renderInLayout($html);
    }
}
