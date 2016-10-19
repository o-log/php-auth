<?php

namespace OLOG\Auth;

use OLOG\Assert;
use OLOG\Auth\Auth;
use OLOG\Auth\User;
use OLOG\Auth\UserToGroup;
use OLOG\CRUD\InterfaceCRUDTableFilterInvisible;
use OLOG\GETAccess;

class CRUDTableFilterOwnerInvisible implements InterfaceCRUDTableFilterInvisible
{
    public function __construct(){
    }

    public function getHtml(){
        return '';
    }

    /**
     * Возвращает пару из sql-условия и массива значений плейсхолдеров. Массив значений может быть пустой если плейсхолдеры не нужны.
     * @return array
     */
    public function sqlConditionAndPlaceholderValue()
    {
        // check full access cookie

        $auth_cookie_name = AuthConfig::getFullAccessCookieName();
        if ($auth_cookie_name) {
            if (isset($_COOKIE[$auth_cookie_name])) {
                return ['', []]; // do not filter
            }
        }

        // check current user

        $current_user_id = Auth::currentUserId();
        if (!$current_user_id){
            return [' 1=2 ', []]; // no current user, select nothing
        }

        $current_user_obj = User::factory($current_user_id);
        if ($current_user_obj->getHasFullAccess()){
            return ['', []]; // do not filter
        }

        $current_user_usertogroup_ids_arr = UserToGroup::getIdsArrForUserIdByCreatedAtDesc($current_user_id);
        $current_user_groups_ids_arr = [];

        foreach ($current_user_usertogroup_ids_arr as $usertogroup_id){
            $usertogroup_obj = UserToGroup::factory($usertogroup_id);
            $current_user_groups_ids_arr[] = $usertogroup_obj->getGroupId();
        }

        $placeholder_values_arr = [];
        $where = ' (';
        $where .= '(owner_user_id = ?)';
        $placeholder_values_arr[] = $current_user_id;

        if (count($current_user_groups_ids_arr) > 0) {
            $user_groups_placeholders_arr = array_fill(0, count($current_user_groups_ids_arr), '?');

            $where .= ' or (owner_group_id in (' . implode($user_groups_placeholders_arr) . '))';
            $placeholder_values_arr[] = array_merge($placeholder_values_arr, $current_user_groups_ids_arr);
        }

        $where .= ') ';

        return [$where, $placeholder_values_arr];
    }
}