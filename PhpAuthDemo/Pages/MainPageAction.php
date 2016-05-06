<?php

namespace PhpAuthDemo\Pages;

use OLOG\BT;

class MainPageAction
    {
        static public function getUrl(){
            return '/';
        }
    
        public function action(){
            echo BT::a(\OLOG\Auth\Admin\UsersListAction::getUrl(), 'Auth admin - users list');
        }
    }