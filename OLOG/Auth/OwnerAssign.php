<?php

namespace OLOG\Auth;

use OLOG\Assert;
use OLOG\FullObjectId;
use OLOG\Model\InterfaceLoad;

class OwnerAssign
{
    /**
     * @param $obj InterfaceOwner
     * Does not saves object - designed to be called from constructor.
     */
    static public function assignCurrentUserAsOwnerToObj($obj){
        Assert::assert($obj instanceof InterfaceOwner);
        Assert::assert($obj instanceof InterfaceLoad);

        static $__inprogress = [];
        $inprogress_key = FullObjectId::getFullObjectId($obj);
        if (array_key_exists($inprogress_key, $__inprogress)) {
            return;
        }

        $__inprogress[$inprogress_key] = 1;

        // заполняем при создании объекта
        if (!$obj->getId()) {
            $current_user_id = Auth::currentUserId();
            if ($current_user_id) {
                $obj->setOwnerUserId($current_user_id);

                $current_user_obj = User::factory($current_user_id);
                $obj->setOwnerGroupId($current_user_obj->getPrimaryGroupId());
            }
        }

        unset($__inprogress[$inprogress_key]);
    }
}