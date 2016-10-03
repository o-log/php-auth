<?php
namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\OperatorPermission;
use OLOG\Auth\Permissions;
use OLOG\Auth\PermissionToUser;
use OLOG\Exits;
use OLOG\InterfaceAction;

class PermissionAddToOperatorAction implements InterfaceAction
{
    protected $operator_id;

    protected $permission_id;

    public function __construct($operator_id, $permission_id) {
        $this->operator_id = $operator_id;
        $this->permission_id = $permission_id;
    }

    public function url(){
        return '/admin/permission_add_to_operator/' . $this->operator_id . '/permission/' . $this->permission_id;
    }

    static public function urlMask() {
        return '/admin/permission_add_to_operator/(\d+)/permission/(\d+)';
    }

    public function action() {
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions(
                [
                    Permissions::PERMISSION_PHPAUTH_MANAGE_OPERATORS,
                    Permissions::PERMISSION_PHPAUTH_MANAGE_USERS
                ]
            )
        );

        $permissiontouser_obj = new OperatorPermission();
        $permissiontouser_obj->setOperatorId($this->operator_id);
        $permissiontouser_obj->setPermissionId($this->permission_id);
        $permissiontouser_obj->save();

        \OLOG\Redirects::redirect(OperatorEditAction::getUrl($this->operator_id));
    }
}