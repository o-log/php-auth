<?php

namespace OLOG\Auth\Admin;

use OLOG\ActionInterface;
use OLOG\Auth\Group;
use OLOG\Auth\Operator;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDTableFilterLikeInline;
use OLOG\Exits;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\PageTitleInterface;

class UsersListAction extends AuthAdminActionsBaseProxy implements
    ActionInterface,
    PageTitleInterface
{
    public function url(){
        return '/admin/auth/users';
    }

    public function pageTitle(){
        return 'Пользователи';
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
                    new CRUDFormRow('Комментарий', new CRUDFormWidgetTextarea('description'))
                ],
	            (new UserEditAction('{this->id}'))->url()
            ),
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    'Логин',
                    new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->login}', (new UserEditAction('{this->id}'))->url())
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Создан',
                    new \OLOG\CRUD\CRUDTableWidgetTimestamp('{this->created_at_ts}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Комментарий',
                    new \OLOG\CRUD\CRUDTableWidgetText('{this->description}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Основная группа',
                    new \OLOG\CRUD\CRUDTableWidgetText('{' . Group::class . '.{this->' . User::_PRIMARY_GROUP_ID . '}->title}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    '',
                    new \OLOG\CRUD\CRUDTableWidgetDelete()
                )
            ],
            [
                new CRUDTableFilterLikeInline('login_1287318', '', 'login', 'Логин'),
                new CRUDTableFilterLikeInline('description_1287318', '', 'description', 'Комментарий'),
                new \OLOG\Auth\CRUDTableFilterOwnerInvisible()
            ],
            'login',
            '1',
            \OLOG\CRUD\CRUDTable::FILTERS_POSITION_INLINE
        );

        AdminLayoutSelector::render($html, $this);
    }
}