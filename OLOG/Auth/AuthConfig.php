<?php

namespace OLOG\Auth;

class AuthConfig
{
    static protected $ssid_cookie_name = 'php-auth-session-id';
    static protected $session_id_cookie_domain = null; // default value must be null for proper setcookie
    static protected $full_access_cookie_name = '';
    static protected $extra_cookies_arr = [];
    static protected $admin_actions_base_classname;
    static protected $user_aftersave_callback_class_name = '';
    static protected $after_logout_callback_class_name = '';
    static protected $session_cookie_is_secure = false;
    static protected $session_cookie_is_http_only = true;

    /**
     * @return boolean
     */
    public static function getSessionCookieIsSecure()
    {
        return self::$session_cookie_is_secure;
    }

    /**
     * @param boolean $session_cookie_is_secure
     */
    public static function setSessionCookieIsSecure($session_cookie_is_secure)
    {
        self::$session_cookie_is_secure = $session_cookie_is_secure;
    }

    /**
     * @return boolean
     */
    public static function getSessionCookieIsHttpOnly()
    {
        return self::$session_cookie_is_http_only;
    }

    /**
     * @param boolean $session_cookie_is_http_only
     */
    public static function setSessionCookieIsHttpOnly($session_cookie_is_http_only)
    {
        self::$session_cookie_is_http_only = $session_cookie_is_http_only;
    }

    /**
     * @return string
     */
    public static function getAfterLogoutCallbackClassName()
    {
        return self::$after_logout_callback_class_name;
    }

    /**
     * @param string $after_logout_callback_class_name
     */
    public static function setAfterLogoutCallbackClassName($after_logout_callback_class_name)
    {
        self::$after_logout_callback_class_name = $after_logout_callback_class_name;
    }

    /**
     * @return null
     */
    public static function getUserAfterSaveCallbackClassName()
    {
        return self::$user_aftersave_callback_class_name;
    }

    /**
     * @param null $user_aftersave_callback_class_name
     */
    public static function setUserAfterSaveCallbackClassName($user_aftersave_callback_class_name)
    {
        self::$user_aftersave_callback_class_name = $user_aftersave_callback_class_name;
    }

    /**
     * @return mixed
     */
    public static function getAdminActionsBaseClassname()
    {
        return self::$admin_actions_base_classname;
    }

    /**
     * @param mixed $admin_actions_base_classname
     */
    public static function setAdminActionsBaseClassname($admin_actions_base_classname)
    {
        self::$admin_actions_base_classname = $admin_actions_base_classname;
    }

    /**
     * @return array
     */
    public static function getExtraCookiesArr()
    {
        return self::$extra_cookies_arr;
    }

    /**
     * @param array $extra_cookies_arr
     */
    public static function setExtraCookiesArr($extra_cookies_arr)
    {
        self::$extra_cookies_arr = $extra_cookies_arr;
    }

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