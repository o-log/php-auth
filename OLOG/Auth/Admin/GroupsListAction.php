<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\CRUDTableFilterOwnerInvisible;
use OLOG\Auth\Group;
use OLOG\Auth\Operator;
use OLOG\Auth\Permissions;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDTableFilterLike;
use OLOG\Exits;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;

class GroupsListAction extends AuthAdminActionsBaseProxy implements
    InterfaceAction,
    InterfacePageTitle
{
    public function url(){
        return '/admin/auth/groups';
    }

    public function pageTitle(){
        return 'Группы';
    }

    /*
    public function currentBreadcrumbsArr(){
        return self::breadcrumbsArr();
    }

    static public function breadcrumbsArr()
    {
        return array_merge(AuthAdminAction::breadcrumbsArr(), [BT::a(self::getUrl(), self::pageTitle())]);
    }
    */

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
            'title',
            '1',
            \OLOG\CRUD\CRUDTable::FILTERS_POSITION_TOP
        );

        AdminLayoutSelector::render($html, $this);
    }
}