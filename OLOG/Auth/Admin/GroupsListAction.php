<?php

namespace OLOG\Auth\Admin;

use OLOG\ActionInterface;
use OLOG\Auth\CRUDTableFilterOwnerInvisible;
use OLOG\Auth\Group;
use OLOG\Auth\Operator;
use OLOG\Auth\Permissions;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDTableFilterLike;
use OLOG\CRUD\CRUDTableFilterLikeInline;
use OLOG\Exits;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\PageTitleInterface;

class GroupsListAction extends AuthAdminActionsBaseProxy implements
    ActionInterface,
    PageTitleInterface
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
                    new CRUDFormRow('Название', new CRUDFormWidgetInput(Group::_TITLE))
                ]
            ),
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    'Название',
                    new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->' . Group::_TITLE. '}', (new GroupEditAction('{this->id}'))->url())
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Создана',
                    new \OLOG\CRUD\CRUDTableWidgetTimestamp('{this->created_at_ts}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    '',
                    new \OLOG\CRUD\CRUDTableWidgetDelete()
                )
            ],
            [
                new CRUDTableFilterLikeInline('wgieruygfigfe', '', Group::_TITLE, 'Название'),
                new CRUDTableFilterOwnerInvisible()
            ],
            'title',
            '1',
            \OLOG\CRUD\CRUDTable::FILTERS_POSITION_INLINE
        );

        AdminLayoutSelector::render($html, $this);
    }
}