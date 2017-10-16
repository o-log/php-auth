<?php

namespace OLOG\Auth;

class OwnerCheck
{
    /**
     * @param $obj InterfaceOwner
     * @return bool
     */
    static public function currentUserOwnsObj($obj)
    {
        $auth_cookie_name = AuthConfig::getFullAccessCookieName();
        if ($auth_cookie_name) {
            if (isset($_COOKIE[$auth_cookie_name])) {
                return true;
            }
        }

        $current_user_id = Auth::currentUserId();
        if (!$current_user_id) {
            return false;
        }

        return self::userOwnsObj($current_user_id, $obj);
    }

    /**
     * @param $user_id
     * @param $obj InterfaceOwner
     * @return bool
     */
    static public function userOwnsObj($user_id, $obj)
    {
        assert($obj instanceof InterfaceOwner, 'Object must implement '. \OLOG\Auth\InterfaceOwner::class .' interface');

        $current_user_obj = User::factory($user_id);
        if ($current_user_obj->getHasFullAccess()) {
            return true;
        }

        $current_user_usertogroup_ids_arr = UserToGroup::getIdsArrForUserIdByCreatedAtDesc($user_id);
        $current_user_groups_ids_arr = [];

        foreach ($current_user_usertogroup_ids_arr as $usertogroup_id) {
            $usertogroup_obj = UserToGroup::factory($usertogroup_id);
            $current_user_groups_ids_arr[] = $usertogroup_obj->getGroupId();
        }

        if ($obj->getOwnerUserId() == $user_id) {
            return true;
        }

        $obj_owner_group_id = $obj->getOwnerGroupId();
        if (in_array($obj_owner_group_id, $current_user_groups_ids_arr)) {
            return true;
        }

        return false;
    }
}