<?php

namespace OLOG\Auth\Pages;

use OLOG\Auth\Auth;
use OLOG\Sanitize;

class LogoutAction
{
    static public function getUrl()
    {
        return '/auth/logout';
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