<?php

namespace PhpAuthDemo\Pages;

use OLOG\Auth\Auth;
use OLOG\BT\BT;
use OLOG\BT\InterfacePageTitle;
use OLOG\BT\InterfaceUserName;
use OLOG\BT\LayoutBootstrap;

class MainPageAction implements
    InterfacePageTitle,
    InterfaceUserName
{
        public function currentUserName()
        {
            return Auth::currentUserLogin();
        }

        public function currentPageTitle()
        {
            return 'PHP-Auth demo';
        }

        static public function getUrl(){
            return '/';
        }
    
        public function action(){
            $html = '';

            $html .= '<div>Current user ID: "' . Auth::currentUserId() . '"</div>';
            $html .= '<div>' . BT::a(\OLOG\Auth\Pages\LoginAction::getUrl(), 'login') . '</div>';
            $html .= '<div>' . BT::a(\OLOG\Auth\Pages\LogoutAction::getUrl(), 'logout') . '</div>';

            $html .= '<div>' . BT::a(\OLOG\Auth\Admin\UsersListAction::getUrl(), 'Auth admin') . '</div>';

            LayoutBootstrap::render($html, $this);
        }
    }