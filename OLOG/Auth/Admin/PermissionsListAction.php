<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\Permissions;
use OLOG\BT\BT;
use OLOG\BT\InterfaceBreadcrumbs;
use OLOG\BT\InterfacePageTitle;
use OLOG\BT\InterfaceUserName;
use OLOG\BT\Layout;
use OLOG\CRUD\CRUDForm;
use OLOG\Exits;

class PermissionsListAction implements
    InterfaceBreadcrumbs,
    InterfacePageTitle, 
    InterfaceUserName
{
    use CurrentUserNameTrait;

    public function currentBreadcrumbsArr()
    {
        return self::breadcrumbsArr();
    }

    public function breadcrumbsArr(){
        return array_merge(AuthAdminAction::breadcrumbsArr(), [BT::a(self::getUrl(), self::pageTitle())]);
    }

    public function currentPageTitle()
    {
        return self::pageTitle();
    }

    static public function pageTitle(){
        return 'Permissions';
    }

    static public function getUrl(){
        return '/admin/auth/permissions';
    }

    public function action(){
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_OPERATORS])
        );

        $html = \OLOG\CRUD\CRUDTable::html(
            \OLOG\Auth\Permission::class,
            null,
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    'ID', new \OLOG\CRUD\CRUDTableWidgetText('{this->id}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'title', new \OLOG\CRUD\CRUDTableWidgetText('{this->title}')
                )
            ]
        );

        Layout::render($html, $this);
    }

}