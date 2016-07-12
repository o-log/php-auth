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

class AuthAdminAction
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
        return 'Auth admin';
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
        $html .= '<div>' . BT::a(PermissionsListAction::getUrl(), 'Permissions') . '</div>';
        $html .= '<div>' . BT::a(UsersListAction::getUrl(), 'Users') . '</div>';
        $html .= '<div>' . BT::a(OperatorsListAction::getUrl(), 'Operators') . '</div>';

        Layout::render($html, $this);
    }
}