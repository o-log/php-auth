<?php

namespace OLOG\Auth;

class AuthConfig
{
    protected static $ssid_cookie_name = 'php-auth-session-id';
    protected static $session_id_cookie_domain = null; // default value must be null for proper setcookie
    protected static $full_access_cookie_name = '';
    protected static $extra_cookies_arr = [];
    protected static $admin_actions_base_classname;
    protected static $after_logout_callback_class_name;
    protected static $session_cookie_is_secure = false;
    protected static $session_cookie_is_http_only = true;
    protected static $default_redirect_url_after_successful_login = '';
    protected static $login_url = '/auth/login';
    protected static $logout_url = '/auth/logout';

    /**
     * @return string
     */
    public static function getLoginUrl()
    {
        return self::$login_url;
    }

    /**
     * @param string $login_url
     */
    public static function setLoginUrl($login_url)
    {
        self::$login_url = $login_url;
    }

    /**
     * @return string
     */
    public static function getLogoutUrl()
    {
        return self::$logout_url;
    }

    /**
     * @param string $logout_url
     */
    public static function setLogoutUrl($logout_url)
    {
        self::$logout_url = $logout_url;
    }

    /**
     * @return string
     */
    public static function getDefaultRedirectUrlAfterSuccessfulLogin()
    {
        return self::$default_redirect_url_after_successful_login;
    }

    /**
     * @param string $default_redirect_url_after_successful_login
     */
    public static function setDefaultRedirectUrlAfterSuccessfulLogin($default_redirect_url_after_successful_login)
    {
        self::$default_redirect_url_after_successful_login = $default_redirect_url_after_successful_login;
    }

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
     * @return InterfaceAfterLogoutCallback
     */
    public static function getAfterLogoutCallbackClassName()
    {
        return self::$after_logout_callback_class_name;
    }

    /**
     * @param InterfaceAfterLogoutCallback $after_logout_callback_class_name
     */
    public static function setAfterLogoutCallbackClassName($after_logout_callback_class_name)
    {
        self::$after_logout_callback_class_name = $after_logout_callback_class_name;
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
     * @return string[]|\OLOG\Auth\ExtraCookie[]
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