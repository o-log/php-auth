<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth\Admin;

use OLOG\ActionInterface;
use OLOG\Auth\Auth;
use OLOG\Auth\CRUDTableFilterOwnerInvisible;
use OLOG\Auth\Group;
use OLOG\Auth\Permissions;
use OLOG\CRUD\CTable;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TWReferenceSelect;
use OLOG\CRUD\TWText;
use OLOG\CRUD\TWTimestamp;

class GroupsListAjaxAction implements ActionInterface
{
    public function url(){
        return '/admin/auth/groups_ajax';
    }

    public function action(){
        /*
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS])
        );
        */
        Auth::check([Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS]);

        $html = CTable::html(
            Group::class,
            '',
            [
                new TCol('', new TWReferenceSelect(Group::_TITLE)),
                new TCol('', new TWText(Group::_TITLE)),
                new TCol('', new TWTimestamp(Group::_CREATED_AT_TS))
            ],
            [
                new CRUDTableFilterOwnerInvisible()
            ],
            Group::_TITLE
        );

        echo $html;
    }
}
