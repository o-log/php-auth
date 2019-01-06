<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth\Admin;

use OLOG\Auth\Auth;
use OLOG\Auth\Permissions;
use OLOG\Auth\PermissionToUser;
use OLOG\MaskActionInterface;

class PermissionAddToUserAction implements MaskActionInterface
{
    protected  $user_id;

    protected $permission_id;

    public function __construct($user_id, $permission_id) {
        $this->user_id = $user_id;
        $this->permission_id = $permission_id;
    }

    public function url(){
        return '/admin/permission_add_to_user/' . $this->user_id . '/permission/' . $this->permission_id;
    }

    static public function mask() {
        return '/admin/permission_add_to_user/(\d+)/permission/(\d+)';
    }

    public function action() {
        /*
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions(
                [
                    Permissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS,
                ]
            )
        );
        */
        Auth::check([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS]);

        $permissiontouser_obj = new PermissionToUser();
        $permissiontouser_obj->setUserId($this->user_id);
        $permissiontouser_obj->setPermissionId($this->permission_id);
        $permissiontouser_obj->save();

        \OLOG\Redirects::redirect((new UserEditAction($this->user_id))->url());
    }
}
