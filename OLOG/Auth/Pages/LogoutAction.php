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
use OLOG\Redirects;

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

        Redirects::redirect($redirect);
    }
}
