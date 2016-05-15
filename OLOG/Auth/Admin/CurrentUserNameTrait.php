<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Auth;

trait CurrentUserNameTrait
{
    public function currentUserName(){
        return Auth::currentUserLogin();
    }
}