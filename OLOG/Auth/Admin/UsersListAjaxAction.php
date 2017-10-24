<?php

namespace OLOG\Auth\Admin;

use OLOG\ActionInterface;
use OLOG\Auth\Auth;
use OLOG\Auth\CRUDTableFilterOwnerInvisible;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\CRUD\CTable;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFLike;
use OLOG\CRUD\TWReferenceSelect;
use OLOG\CRUD\TWText;
use OLOG\Exits;

class UsersListAjaxAction implements ActionInterface
{
    public function url(){
        return '/admin/auth/users_ajax';
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
            '',
            [
                new TCol(
                    'Логин',
                    new TWReferenceSelect('login')
                ),
                new TCol(
                    'Логин',
                    new TWText('{this->login}')
                ),
                new TCol(
                    'Создан',
                    new TWText('{this->created_at_ts}')
                )
            ],
            [
                new TFLike('wert76wer76t', 'login', 'login'),
                new CRUDTableFilterOwnerInvisible()
            ],
            'login',
            'gy876tweu'
        );

        echo $html;
    }
}
