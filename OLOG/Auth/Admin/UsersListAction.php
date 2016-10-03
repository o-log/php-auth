<?php

namespace OLOG\Auth\Admin;

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
use OLOG\CRUD\CRUDTableFilter;
use OLOG\CRUD\CRUDTableFilterLike;
use OLOG\CRUD\CRUDTableWidgetText;
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
        return 'Пользователи';
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
                    new CRUDFormRow('login', new CRUDFormWidgetInput('login')),
                    new CRUDFormRow('Комментарий', new CRUDFormWidgetTextarea('comment'))
                ]
            ),
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    'Логин',
                    new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->login}', UserEditAction::getUrl('{this->id}'))
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Создан',
                    new \OLOG\CRUD\CRUDTableWidgetTimestamp('{this->created_at_ts}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Комментарий',
                    new \OLOG\CRUD\CRUDTableWidgetText('{this->comment}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Удалить',
                    new \OLOG\CRUD\CRUDTableWidgetDelete()
                )
            ],
            [
                new CRUDTableFilterLike('login_1287318', 'login', 'login'),
                new CRUDTableFilterLike('comment_1287318', 'комментарий', 'comment')
            ],
            '',
            '1',
            \OLOG\CRUD\CRUDTable::FILTERS_POSITION_TOP
        );

        Layout::render($html, $this);
    }
}