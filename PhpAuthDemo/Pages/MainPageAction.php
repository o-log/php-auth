<?php

namespace PhpAuthDemo\Pages;

use OLOG\Auth\Auth;
use OLOG\Auth\User;
use OLOG\BT\BT;
use OLOG\BT\LayoutBootstrap;
use OLOG\InterfaceAction;
use OLOG\Layouts\InterfacePageTitle;

class MainPageAction implements
    InterfaceAction,
    InterfacePageTitle
{
        public function pageTitle()
        {
            return 'PHP-Auth demo';
        }

        public function url(){
            return '/';
        }
    
        public function action(){
            $html = '';

            $html .= '<div>Current user ID: "' . Auth::currentUserId() . '"</div>';
            $html .= '<div>' . BT::a(\OLOG\Auth\Pages\LoginAction::getUrl(), 'login') . '</div>';
            $html .= '<div>' . BT::a(\OLOG\Auth\Pages\LogoutAction::getUrl(), 'logout') . '</div>';

            $html .= '<div>' . BT::a((new \OLOG\Auth\Admin\UsersListAction())->url(), 'Auth admin') . '</div>';

            $user = new User();

            $user->setLogin('Test user Login');

            $html .= ' Login before save: '.$user->getLogin();
            $user->save();
            $html .= '<br>';
            $html .= ' Login after save: '.$user->getLogin();

            LayoutBootstrap::render($html, $this);
        }
    }