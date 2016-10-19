<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\Permissions;
use OLOG\BT\BT;
use OLOG\BT\InterfaceBreadcrumbs;
use OLOG\BT\InterfacePageTitle;
use OLOG\BT\InterfaceUserName;
use OLOG\BT\Layout;
use OLOG\Exits;

class AuthAdminAction extends AuthAdminBaseAction 
    implements InterfaceBreadcrumbs,
    InterfacePageTitle,
    InterfaceUserName
{
    use CurrentUserNameTrait;

    static public function getUrl(){
        return '/admin/auth'; // TODO: common prefix from config
    }

    public function currentBreadcrumbsArr(){
        return self::breadcrumbsArr();
    }

    static public function breadcrumbsArr()
    {
        return [BT::a(self::getUrl(), self::pageTitle())];
    }
    
    public function currentPageTitle()
    {
        return self::pageTitle();
    }
    
    static public function pageTitle(){
        return 'Авторизация';
    }

    public function action(){
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions(
                [
                    Permissions::PERMISSION_PHPAUTH_MANAGE_OPERATORS,
                    Permissions::PERMISSION_PHPAUTH_MANAGE_USERS
                ]
            )
        );

        $html = '';
        $html .= '<div>' . BT::a(PermissionsListAction::getUrl(), 'Разрешения') . '</div>';
        $html .= '<div>' . BT::a(UsersListAction::getUrl(), 'Пользователи') . '</div>';
        $html .= '<div>' . BT::a(GroupsListAction::getUrl(), 'Группы') . '</div>';
        $html .= '<div>' . BT::a(OperatorsListAction::getUrl(), 'Операторы') . '</div>';

        Layout::render($html, $this);
    }
}