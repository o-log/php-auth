<?php


namespace OLOG\Auth;


class ExtraCookie
{
    protected $cookie_name = '';
    protected $cookie_value = '';
    protected $secure = false;
    protected $http_only = true;

    /**
     * ExtraCookie constructor.
     * @param string $cookie_name
     * @param string $cookie_value
     * @param bool $secure
     * @param bool $http_only
     */
    public function __construct($cookie_name, $cookie_value, $secure, $http_only)
    {
        $this->setCookieName($cookie_name);
        $this->setCookieValue($cookie_value);
        $this->setSecure($secure);
        $this->setHttpOnly($http_only);
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

    /**
     * @return string
     */
    public function getCookieValue()
    {
        return $this->cookie_value;
    }

    /**
     * @param string $cookie_value
     */
    public function setCookieValue($cookie_value)
    {
        $this->cookie_value = $cookie_value;
    }

    /**
     * @return string
     */
    public function getCookieName()
    {
        return $this->cookie_name;
    }

    /**
     * @param string $cookie_name
     */
    public function setCookieName($cookie_name)
    {
        $this->cookie_name = $cookie_name;
    }

    /**
     * @return boolean
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * @param boolean $secure
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * @return boolean
     */
    public function isHttpOnly()
    {
        return $this->http_only;
    }

    /**
     * @param boolean $http_only
     */
    public function setHttpOnly($http_only)
    {
        $this->http_only = $http_only;
    }
}