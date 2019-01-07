<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth\Admin;

use OLOG\ActionInterface;
use OLOG\Auth\Auth;
use OLOG\Auth\Permission;
use OLOG\Auth\Permissions;
use OLOG\CRUD\CTable;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TWText;
use OLOG\CRUD\TWTextWithLink;
use OLOG\Layouts\PageTitleInterface;

class PermissionsListAction extends AuthAdminActionsBaseProxy implements
    ActionInterface,
    PageTitleInterface
{
    public function pageTitle()
    {
        return 'Разрешения';
    }

    public function url()
    {
        return '/admin/auth/permissions';
    }

    public function action()
    {
        Auth::check([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS]);

        $html = CTable::html(
            Permission::class,
            null,
            [
                new TCol(
                    '', new TWText(Permission::_ID)
                ),
                new TCol(
                    '', new TWTextWithLink(
                        Permission::_TITLE,
                        function (Permission $permission){
                            return (new PermissionToUserListAction($permission->id))->url();
                        }
                    )
                ),
            ],
            [],
            Permission::_TITLE
        );

        $this->renderInLayout($html);
    }
}
