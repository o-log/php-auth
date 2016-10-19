<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Auth;
use OLOG\Auth\Group;
use OLOG\Auth\Operator;
use OLOG\Auth\OwnerCheck;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\BT\Layout;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\Exits;
use OLOG\InterfaceAction;

class GroupEditAction extends AuthAdminBaseAction implements
    InterfaceAction
{
    private $group_id;

    public function __construct($group_id)
    {
        $this->setGroupId($group_id);
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * @param mixed $group_id
     */
    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;
    }


    static public function urlMask()
    {
        return '/admin/auth/group/(\d+)';
    }

    public function url()
    {
        return '/admin/auth/group/' . $this->getGroupId();
    }

    public function action(){
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS])
        );

        $group_obj = Group::factory($this->getGroupId());

        Exits::exit403If(
            !OwnerCheck::currentUserOwnsObj($group_obj)
        );

        $html = '';

        $html .= CRUDForm::html(
            $group_obj,
            [
                new CRUDFormRow(
                    'Title',
                    new CRUDFormWidgetInput(Group::_TITLE)
                )
            ]
        );

        $html .= self::adminParamsForm($this->group_id);

        Layout::render($html, $this);
    }

    /**
     * Владельца пока показывает только пользователям с полным доступом.
     * @param $group_id
     * @return string
     */
    static public function adminParamsForm($group_id){
        /** @var User $current_user_obj */
        $current_user_obj = Auth::currentUserObj();
        if (!$current_user_obj){
            return '';
        }

        if (!$current_user_obj->getHasFullAccess()) {
            return '';
        }

        $html = '';

        $html .= '<h2>Владельцы</h2>';

        $group_obj = Group::factory($group_id);
        $html .= CRUDForm::html(
            $group_obj,
            [
                new CRUDFormRow(
                    'Owner user',
                    new CRUDFormWidgetInput(User::_OWNER_USER_ID, true)
                ),
                new CRUDFormRow(
                    'Owner group',
                    new CRUDFormWidgetInput(User::_OWNER_GROUP_ID, true)
                )
            ]
        );

        return $html;
    }


}