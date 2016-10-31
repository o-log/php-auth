<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 31.10.16
 * Time: 11:06
 */

namespace OLOG\Auth;


interface InterfaceUserAfterSaveCallback
{
    static public function userAfterSaveCallback($user_obj);

}