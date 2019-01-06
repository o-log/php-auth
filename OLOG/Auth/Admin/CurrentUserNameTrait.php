<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth\Admin;

use OLOG\Auth\Auth;

trait CurrentUserNameTrait
{
    public function currentUserName(){
        return Auth::currentUserLogin();
    }
}
