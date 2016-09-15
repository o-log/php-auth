<?php

namespace OLOG\Auth\Pages;

use OLOG\Auth\Auth;
use OLOG\Auth\AuthConfig;
use OLOG\Sanitize;

class LogoutAction
{
    static public function getUrl(){
        return '/auth/logout';
    }

    public function action(){
        Auth::logout();

        // remove extra cookies
        if (!empty(AuthConfig::getExtraCookiesArr())){
            $extra_cookies_arr = AuthConfig::getExtraCookiesArr();

            foreach ($extra_cookies_arr as $cookie_name => $cookie_value){
                //setcookie($cookie_name, $cookie_value, time() + Auth::SESSION_LIFETIME_SECONDS, '/', Auth::sessionCookieDomain());
                setcookie($cookie_name, "", 1000, '/', Auth::sessionCookieDomain(), true, true);
            }
        }

        $redirect = '/';
        if (isset($_GET['destination'])) {
            $redirect = Sanitize::sanitizeUrl($_GET['destination']);
        }

        \OLOG\Redirects::redirect($redirect);
    }
}