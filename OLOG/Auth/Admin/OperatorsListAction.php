<?php

namespace OLOG\Auth\Admin;

use OLOG\ActionInterface;
use OLOG\Auth\Operator;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetReference;
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDTableFilterLike;
use OLOG\Exits;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\PageTitleInterface;

class OperatorsListAction extends AuthAdminActionsBaseProxy implements
    ActionInterface,
    PageTitleInterface
{
    public function pageTitle(){
        return 'Операторы';
    }

    public function url(){
        return '/admin/auth/operators';
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
                    'ID', new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->id}', (new OperatorEditAction('{this->id}'))->url())
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

        AdminLayoutSelector::render($html, $this);
    }

}