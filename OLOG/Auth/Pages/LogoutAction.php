<?php

namespace OLOG\Auth\Pages;

use OLOG\Auth\Auth;
use OLOG\Auth\AuthConfig;
use OLOG\Sanitize;

class LogoutAction
{
    static public function getUrl()
    {
        return AuthConfig::getLogoutUrl();
    }

    public function action()
    {
        Auth::logout();

        //ExtraCookiesLib::unsetExtraCookies();

        $redirect = '/';
        if (isset($_GET['destination'])) {
            $redirect = Sanitize::sanitizeUrl($_GET['destination']);
        }

        \OLOG\Redirects::redirect($redirect);
    }
}