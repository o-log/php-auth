<?php

namespace OLOG\Auth\Pages;

use OLOG\Auth\Auth;
use OLOG\Auth\AuthConfig;
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

        self::unsetExtraCookies();

        $redirect = '/';
        if (isset($_GET['destination'])) {
            $redirect = Sanitize::sanitizeUrl($_GET['destination']);
        }

        \OLOG\Redirects::redirect($redirect);
    }

    public static function unsetExtraCookies()
    {
        $extra_cookies_arr = AuthConfig::getExtraCookiesArr();
        if (empty($extra_cookies_arr)) {
            return;
        }

        $extra_cookies_arr = AuthConfig::getExtraCookiesArr();

        foreach ($extra_cookies_arr as $cookie_name => $cookie_value) {
            if ($cookie_value instanceof \OLOG\Auth\ExtraCookie) {
                $extra_cookie_obj = $cookie_value;
                setcookie(
                    $extra_cookie_obj->getCookieName(),
                    '',
                    1000,
                    '/',
                    Auth::sessionCookieDomain(),
                    $extra_cookie_obj->isSecure(),
                    $extra_cookie_obj->isHttpOnly()
                );
            } else {
                setcookie($cookie_name, '', 1000, '/', Auth::sessionCookieDomain(), false, true);
            }
        }
    }
}