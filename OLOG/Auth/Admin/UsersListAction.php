<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\Permission;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\BT\BT;
use OLOG\BT\InterfaceBreadcrumbs;
use OLOG\BT\InterfacePageTitle;
use OLOG\BT\InterfaceUserName;
use OLOG\BT\Layout;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\Exits;

class UsersListAction implements
    InterfaceBreadcrumbs,
    InterfacePageTitle,
    InterfaceUserName
{
    use CurrentUserNameTrait;
    
    static public function getUrl(){
        return '/admin/auth/users';
    }

    public function currentPageTitle()
    {
        return self::pageTitle();
    }

    static public function pageTitle(){
        return 'Users';
    }

    public function currentBreadcrumbsArr(){
        return self::breadcrumbsArr();
    }

    static public function breadcrumbsArr()
    {
        return array_merge(AuthAdminAction::breadcrumbsArr(), [BT::a(self::getUrl(), self::pageTitle())]);
    }


    public function action(){
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS])
        );

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
                    'ID',
                    new \OLOG\CRUD\CRUDTableWidgetText('{this->id}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'login',
                    new \OLOG\CRUD\CRUDTableWidgetText('{this->login}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    '',
                    new \OLOG\CRUD\CRUDTableWidgetTextWithLink('Edit', UserEditAction::getUrl('{this->id}'), 'btn btn-xs btn-default')
                )
            ]
        );

        Layout::render($html, $this);
    }
}