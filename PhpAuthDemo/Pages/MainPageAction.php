<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace PhpAuthDemo\Pages;

use OLOG\ActionInterface;
use OLOG\Auth\Auth;
use OLOG\HTML;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\PageTitleInterface;

class MainPageAction implements
    ActionInterface,
    PageTitleInterface
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
            $html .= '<div>' . HTML::a((new \OLOG\Auth\Pages\LoginAction())->url(), 'login') . '</div>';
            $html .= '<div>' . HTML::a((new \OLOG\Auth\Pages\LogoutAction())->url(), 'logout') . '</div>';
            $html .= '<div>' . HTML::a((new \OLOG\Auth\Admin\UsersListAction())->url(), 'Auth admin') . '</div>';

            AdminLayoutSelector::render($html, $this);
        }
    }
