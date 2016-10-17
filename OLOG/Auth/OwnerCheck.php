<?php

namespace OLOG\Auth;

class OwnerCheck
{
    /**
     * @param $obj InterfaceOwner
     * @return bool
     */
    static public function currentUserOwnsObj($obj){
        $current_user_id = Auth::currentUserId();

        if (!$current_user_id){
            return false;
        }

        if (!($obj instanceof InterfaceOwner)){
            return false;
        }

        $current_user_obj = User::factory($current_user_id);
        if ($current_user_obj->getHasFullAccess()){
            return true;
        }

        $current_user_usertogroup_ids_arr = UserToGroup::getIdsArrForUserIdByCreatedAtDesc($current_user_id);
        $current_user_groups_ids_arr = [];

        foreach ($current_user_usertogroup_ids_arr as $usertogroup_id){
            $usertogroup_obj = UserToGroup::factory($usertogroup_id);
            $current_user_groups_ids_arr[] = $usertogroup_obj->getGroupId();
        }

        if ($obj->getOwnerUserId() == $current_user_id){
            return true;
        }

        $obj_owner_group_id = $obj->getOwnerGroupId();
        if (in_array($obj_owner_group_id, $current_user_groups_ids_arr)){
            return true;
        }

        return false;
    }
}