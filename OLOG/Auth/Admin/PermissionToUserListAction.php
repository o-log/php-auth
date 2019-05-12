<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth\Admin;

use OLOG\Auth\Auth;
use OLOG\Auth\Permission;
use OLOG\Auth\AuthPermissions;
use OLOG\Auth\PermissionToUser;
use OLOG\CRUD\CTable;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFEqualHidden;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWTextWithLink;
use OLOG\Layouts\PageTitleInterface;
use OLOG\MaskActionInterface;

class PermissionToUserListAction extends AuthAdminActionsBaseProxy implements
    MaskActionInterface,
    PageTitleInterface
{
    protected $permission_id;

    public function topActionObj()
    {
        return new PermissionsListAction();
    }

    public function __construct($permission_id)
    {
        $this->permission_id = $permission_id;
    }

    public function pageTitle()
    {
        $permission = Permission::factory($this->permission_id);
        return $permission->getTitle();
    }

    public function url()
    {
        return '/admin/auth/permission_to_user/' . $this->permission_id;
    }

    public static function mask()
    {
        return '/admin/auth/permission_to_user/(\d+)';
    }

    public function action()
    {
        /*
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS])
        );
        */
        Auth::check([AuthPermissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS]);

        $html = '';
        //$permission_obj = Permission::factory($this->permission_id);
        //$html = \OLOG\HTML::tag('h3', [], $permission_obj->getTitle());
        $html .= CTable::html(
            PermissionToUser::class,
            null,
            [
                new TCol(
                    '',
                    new TWTextWithLink(
                        //'{' . User::class . '.{this->user_id}->login}',
                        function (PermissionToUser $ptu){
                            return $ptu->user()->getLogin();
                        },
                        function (PermissionToUser $ptu) {
                            return (new UserEditAction($ptu->getUserId()))->url();
                        }
                    )
                ),
                new TCol(
                    '', new TWDelete()
                )
            ],
            [
                new TFEqualHidden('permission_id', $this->permission_id)
            ],
            '',
            '',
            'Пользователи, которым назначено разрешение'
        );

        $this->renderInLayout($html);
    }
}
