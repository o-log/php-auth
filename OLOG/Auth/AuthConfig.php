<?php

namespace OLOG\Auth;

class AuthConfig
{
    static protected $ssid_cookie_name = 'php-auth-session-id';
    static protected $session_id_cookie_domain = null; // default value must be null for proper setcookie
    static protected $full_access_cookie_name = '';

    /**
     * @return string
     */
    public static function getSsidCookieName()
    {
        return self::$ssid_cookie_name;
    }

    /**
     * @param string $ssid_cookie_name
     */
    public static function setSsidCookieName($ssid_cookie_name)
    {
        self::$ssid_cookie_name = $ssid_cookie_name;
    }

    /**
     * @return null
     */
    public static function getSessionIdCookieDomain()
    {
        return self::$session_id_cookie_domain;
    }

    /**
     * @param null $session_id_cookie_domain
     */
    public static function setSessionIdCookieDomain($session_id_cookie_domain)
    {
        self::$session_id_cookie_domain = $session_id_cookie_domain;
    }

    /**
     * @return string
     */
    public static function getFullAccessCookieName()
    {
        return self::$full_access_cookie_name;
    }

    /**
     * @param string $full_access_cookie_name
     */
    public static function setFullAccessCookieName($full_access_cookie_name)
    {
        self::$full_access_cookie_name = $full_access_cookie_name;
    }
}