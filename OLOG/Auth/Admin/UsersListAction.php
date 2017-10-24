<?php

namespace OLOG\Auth\Admin;

use OLOG\ActionInterface;
use OLOG\Auth\Auth;
use OLOG\Auth\CRUDTableFilterOwnerInvisible;
use OLOG\Auth\Group;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\CRUD\CForm;
use OLOG\CRUD\CTable;
use OLOG\CRUD\FRow;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\FWTextarea;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFLikeInline;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWText;
use OLOG\CRUD\TWTextWithLink;
use OLOG\CRUD\TWTimestamp;
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
        /*
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS])
        );
        */
        Auth::check([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS]);

        $html = CTable::html(
            User::class,
            CForm::html(
                new User(),
                [
                    new FRow('login', new FWInput('login')),
                    new FRow('Комментарий', new FWTextarea('description'))
                ],
	            (new UserEditAction('{this->id}'))->url()
            ),
            [
                new TCol('', new TWTextWithLink(User::_LOGIN, (new UserEditAction('{this->id}'))->url())),
                new TCol('', new TWTimestamp(User::_CREATED_AT_TS)),
                new TCol('', new TWText(User::_DESCRIPTION)),
                new TCol('', new TWText('{' . Group::class . '.{this->' . User::_PRIMARY_GROUP_ID . '}->title}')),
                new TCol('', new TWDelete())
            ],
            [
                new TFLikeInline('login_1287318', '', 'login', 'Фильтр по логину'),
                new TFLikeInline('description_1287318', '', 'description', 'Фильтр по комментарию'),
                new CRUDTableFilterOwnerInvisible()
            ],
            'login',
            '1'
        );

        AdminLayoutSelector::render($html, $this);
    }
}