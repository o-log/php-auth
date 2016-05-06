<?php

namespace PhpAuthDemo\Pages;

use OLOG\Auth\Auth;
use OLOG\BT;

class MainPageAction
    {
        static public function getUrl(){
            return '/';
        }
    
        public function action(){
            echo '<div>Current user ID: "' . Auth::currentUserId() . '"</div>';
            echo '<div>' . BT::a(\OLOG\Auth\Pages\LoginAction::getUrl(), 'login') . '</div>';

            echo BT::a(\OLOG\Auth\Admin\UsersListAction::getUrl(), 'Auth admin - users list');
        }
    }