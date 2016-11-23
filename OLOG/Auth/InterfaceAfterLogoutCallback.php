<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 31.10.16
 * Time: 11:06
 */

namespace OLOG\Auth;


interface InterfaceAfterLogoutCallback
{
    static public function afterLogoutCallback();
}