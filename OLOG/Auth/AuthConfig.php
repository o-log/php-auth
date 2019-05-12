<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth;

class AuthConfig
{
    const SPACE_PHPAUTH = 'space_phpauth';

    static public $sessions_bucket = '';

    protected static $ssid_cookie_name = 'php-auth-session-id';
    protected static $session_id_cookie_domain = '';
    protected static $full_access_cookie_name = '';
    protected static $extra_cookies_arr = [];
    protected static $admin_actions_base_classname = '';
    protected static $after_logout_callback_class_name = '';
    protected static $session_cookie_is_secure = false;
    protected static $session_cookie_is_http_only = true;
    protected static $default_redirect_url_after_successful_login = '';
    protected static $login_url = '/auth/login';
    protected static $logout_url = '/auth/logout';

    public static function getLoginUrl(): string
    {
        return self::$login_url;
    }

    public static function setLoginUrl(string $login_url)
    {
        self::$login_url = $login_url;
    }

    public static function getLogoutUrl(): string
    {
        return self::$logout_url;
    }

    public static function setLogoutUrl(string $logout_url)
    {
        self::$logout_url = $logout_url;
    }

    public static function getDefaultRedirectUrlAfterSuccessfulLogin(): string
    {
        return self::$default_redirect_url_after_successful_login;
    }

    public static function setDefaultRedirectUrlAfterSuccessfulLogin(string $default_redirect_url_after_successful_login)
    {
        self::$default_redirect_url_after_successful_login = $default_redirect_url_after_successful_login;
    }

    public static function getSessionCookieIsSecure(): bool
    {
        return self::$session_cookie_is_secure;
    }

    public static function setSessionCookieIsSecure(bool $session_cookie_is_secure)
    {
        self::$session_cookie_is_secure = $session_cookie_is_secure;
    }

    public static function getSessionCookieIsHttpOnly(): bool
    {
        return self::$session_cookie_is_http_only;
    }

    public static function setSessionCookieIsHttpOnly(bool $session_cookie_is_http_only)
    {
        self::$session_cookie_is_http_only = $session_cookie_is_http_only;
    }

    public static function getAfterLogoutCallbackClassName(): string
    {
        return self::$after_logout_callback_class_name;
    }

    public static function setAfterLogoutCallbackClassName(string $after_logout_callback_class_name)
    {
        self::$after_logout_callback_class_name = $after_logout_callback_class_name;
    }

    public static function getAdminActionsBaseClassname(): string
    {
        return self::$admin_actions_base_classname;
    }

    public static function setAdminActionsBaseClassname(string $admin_actions_base_classname)
    {
        self::$admin_actions_base_classname = $admin_actions_base_classname;
    }

    public static function getExtraCookiesArr(): array
    {
        return self::$extra_cookies_arr;
    }

    public static function setExtraCookiesArr(array $extra_cookies_arr)
    {
        self::$extra_cookies_arr = $extra_cookies_arr;
    }

    public static function getSsidCookieName(): string
    {
        return self::$ssid_cookie_name;
    }

    public static function setSsidCookieName(string $ssid_cookie_name)
    {
        self::$ssid_cookie_name = $ssid_cookie_name;
    }

    public static function getSessionIdCookieDomain(): string
    {
        return self::$session_id_cookie_domain;
    }

    public static function setSessionIdCookieDomain(string $session_id_cookie_domain)
    {
        self::$session_id_cookie_domain = $session_id_cookie_domain;
    }
}
