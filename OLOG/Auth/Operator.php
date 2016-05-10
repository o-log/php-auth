<?php

namespace OLOG\Auth;

class Operator
{
    static public function currentOperatorHasAnyOfPermissions($requested_permissions_arr){
        if (!isset($_COOKIE['php_auth'])){
            return false;
        }

        return true; // TODO: todo
    }
}