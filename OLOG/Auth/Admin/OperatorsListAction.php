<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\BT\BT;
use OLOG\BT\InterfaceUserName;
use OLOG\BT\Layout;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetReference;
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDTableFilterLike;
use OLOG\Exits;

class OperatorsListAction extends AuthAdminBaseAction implements
    \OLOG\BT\InterfaceBreadcrumbs,
    \OLOG\BT\InterfacePageTitle,
    InterfaceUserName
{
    use CurrentUserNameTrait;

    public function currentBreadcrumbsArr()
    {
        return self::breadcrumbsArr();
    }

    static public function breadcrumbsArr(){
        return array_merge(AuthAdminAction::breadcrumbsArr(), [BT::a(self::getUrl(), self::pageTitle())]);
    }

    public function currentPageTitle()
    {
        return self::pageTitle();
    }

    static public function pageTitle(){
        return 'Операторы';
    }

    static public function getUrl(){
        return '/admin/auth/operators'; // TODO common prefix from config
    }

    public function action(){
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_OPERATORS])
        );

        $html = \OLOG\CRUD\CRUDTable::html(
            \OLOG\Auth\Operator::class,
            CRUDForm::html(
                new Operator(),
                [
                    new CRUDFormRow(
                    'user_id',
                    new CRUDFormWidgetReference('user_id', User::class, 'login')
                    ),
                    new CRUDFormRow(
                        'title',
                        new CRUDFormWidgetInput('title')
                    ),
                    new CRUDFormRow(
                        'Описание',
                        new CRUDFormWidgetTextarea('description')
                    )
                ]
            ),
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    'ID', new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->id}', OperatorEditAction::getUrl('{this->id}'))
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'title', new \OLOG\CRUD\CRUDTableWidgetText('{this->title}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'описание', new \OLOG\CRUD\CRUDTableWidgetText('{this->description}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'login', new \OLOG\CRUD\CRUDTableWidgetText('{\OLOG\Auth\User.{this->user_id}->login}')
                )
            ],
            [
                new CRUDTableFilterLike('title_1287318', 'title', 'title'),
                new CRUDTableFilterLike('description_1287318', 'описание', 'description')
            ],
            '',
            '1',
            \OLOG\CRUD\CRUDTable::FILTERS_POSITION_TOP

        );

        Layout::render($html, $this);
    }

}