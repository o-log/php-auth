<?php


namespace OLOG\Auth;


class ExtraCookiesLib
{

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

    public static function setExtraCookies()
    {
        $extra_cookies_arr = AuthConfig::getExtraCookiesArr();
        if (empty($extra_cookies_arr)) {
            return;
        }

        foreach ($extra_cookies_arr as $cookie_name => $cookie_value) {
            if ($cookie_value instanceof \OLOG\Auth\ExtraCookie) {
                $extra_cookie_obj = $cookie_value;
                setcookie(
                    $extra_cookie_obj->getCookieName(),
                    $extra_cookie_obj->getCookieValue(),
                    time() + Auth::SESSION_LIFETIME_SECONDS,
                    '/',
                    Auth::sessionCookieDomain(),
                    $extra_cookie_obj->isSecure(),
                    $extra_cookie_obj->isHttpOnly()
                );
            } else {
                setcookie($cookie_name, $cookie_value, time() + Auth::SESSION_LIFETIME_SECONDS, '/', Auth::sessionCookieDomain(), false, true);
            }
        }
    }
}