<?php

namespace OLOG\Auth\Admin;

use OLOG\ActionInterface;
use OLOG\Auth\Auth;
use OLOG\Auth\CRUDTableFilterOwnerInvisible;
use OLOG\Auth\Group;
use OLOG\Auth\Permissions;
use OLOG\CRUD\CForm;
use OLOG\CRUD\CTable;
use OLOG\CRUD\FGroup;
use OLOG\CRUD\FRow;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFLikeInline;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWTextWithLink;
use OLOG\CRUD\TWTimestamp;
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
        /*
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS])
        );
        */
        Auth::check([Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS]);

        $html = CTable::html(
            Group::class,
            CForm::html(
                new Group(),
                [
                    new FGroup('Название', new FWInput(Group::_TITLE))
                ]
            ),
            [
                new TCol('', new TWTextWithLink('{this->' . Group::_TITLE. '}', (new GroupEditAction('{this->id}'))->url())),
                new TCol('', new TWTimestamp('{this->created_at_ts}')),
                new TCol('', new TWDelete())
            ],
            [
                new TFLikeInline('wgieruygfigfe', '', Group::_TITLE, 'Фильтр по названию'),
                new CRUDTableFilterOwnerInvisible()
            ],
            'title',
            '1'
        );

        AdminLayoutSelector::render($html, $this);
    }
}