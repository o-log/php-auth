<?php

namespace OLOG\Auth;


trait WriteUserLogTrait
{
    protected function writeUserLog($function_name, $user_id){
        $user_obj = User::factory($user_id);
        $user_obj->writeToLog($function_name);
    }


}