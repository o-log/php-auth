<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\CRUDTableFilterOwnerInvisible;
use OLOG\Auth\Group;
use OLOG\Auth\Operator;
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
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDTableFilterLike;
use OLOG\Exits;

class GroupsListAction extends AuthAdminBaseAction implements
    InterfaceBreadcrumbs,
    InterfacePageTitle,
    InterfaceUserName
{
    use CurrentUserNameTrait;

    static public function getUrl(){
        return '/admin/auth/groups';
    }

    public function currentPageTitle()
    {
        return self::pageTitle();
    }

    static public function pageTitle(){
        return 'Группы';
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
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS])
        );

        $html = \OLOG\CRUD\CRUDTable::html(
            \OLOG\Auth\Group::class,
            CRUDForm::html(
                new Group(),
                [
                    new CRUDFormRow('title', new CRUDFormWidgetInput(Group::_TITLE))
                ]
            ),
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    'Title',
                    new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->' . Group::_TITLE. '}', (new GroupEditAction('{this->id}'))->url())
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Создана',
                    new \OLOG\CRUD\CRUDTableWidgetTimestamp('{this->created_at_ts}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Удалить',
                    new \OLOG\CRUD\CRUDTableWidgetDelete()
                )
            ],
            [
                new CRUDTableFilterLike('wgieruygfigfe', 'Title', Group::_TITLE),
                new CRUDTableFilterOwnerInvisible()
            ],
            '',
            '1',
            \OLOG\CRUD\CRUDTable::FILTERS_POSITION_TOP
        );

        Layout::render($html, $this);
    }
}