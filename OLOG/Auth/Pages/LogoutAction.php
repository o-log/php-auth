<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth\Pages;

use OLOG\ActionInterface;
use OLOG\Auth\Auth;
use OLOG\Auth\AuthConfig;
use OLOG\HTML;

class LogoutAction implements ActionInterface
{
    public function url()
    {
        return AuthConfig::getLogoutUrl();
    }

    public function action()
    {
        Auth::logout();

        $redirect = '/';
        if (isset($_GET['destination'])) {
            $redirect = HTML::url($_GET['destination']);
        }

        \OLOG\Redirects::redirect($redirect);
    }
}
