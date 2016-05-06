<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\User;
use OLOG\BT\Layout;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;

class UserEditAction
{
    static public function getUrl($user_id = '(\d+)'){
        return '/admin/user/' . $user_id;
    }

    public function action($user_id){

        // TODO: check permissions

        $user_obj = User::factory($user_id);

        $html = CRUDForm::html(
            $user_obj,
            [
                new CRUDFormRow(
                    'Login',
                    new CRUDFormWidgetInput('login')
                )
            ]
        );

        Layout::render($html);
    }
}