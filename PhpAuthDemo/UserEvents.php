<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 28.10.16
 * Time: 18:01
 */

namespace PhpAuthDemo;


class UserEvents
{
    public static function beforeSave( \OLOG\Auth\User &$user_obj ){
        $user_obj->setLogin('Login chenged in beforeSave() Action '.time());
    }

    public static function afterSave(  \OLOG\Auth\User &$user_obj  ){

        //echo "Hello from aftersave ";

    }

}