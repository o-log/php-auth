<?php

namespace OLOG\Auth;

class Auth
{
    const SESSION_LIFETIME_SECONDS = 60 * 60 * 24 * 7;

    /**
     * @return null|int Returns null if no user currently logged
     */
    static public function currentUserId()
    {

        static $user_id = null;
        static $user_id_approved = false;

        if ($user_id_approved) {
            return $user_id;
        }
        $user_id_approved = true;
        $session_id_from_cookie = self::getSessionIdFromCookie();
        if ($session_id_from_cookie) {
            $user_id = self::getSessionUserIdBySessionId($session_id_from_cookie);
        }
        return $user_id;
    }

    static public function currentUserObj()
    {
        $current_user_id = self::currentUserId();

        if (is_null($current_user_id)) {
            return null;
        }

        return User::factory($current_user_id);
    }

    static public function currentUserLogin()
    {
        $user_id = Auth::currentUserId();
        if (!$user_id) {
            return '';
        }

        $user_obj = User::factory($user_id);
        return $user_obj->getLogin();
    }

    public static function sessionCookieName()
    {
        //return \OLOG\ConfWrapper::value('php-auth.ssid_cookie_name', 'php-auth-session-id');
        return AuthConfig::getSsidCookieName();
    }

    public static function getSessionIdFromCookie()
    {
        // TODO: constants
        // TODO: rewiew best practices!!!
        $cookie_name = self::sessionCookieName();

        if (!array_key_exists($cookie_name, $_COOKIE)) {
            return '';
        }
        return $_COOKIE[$cookie_name];
    }

    static public function sessionCacheKey($user_session_id)
    {
        return 'php_auth_user_' . $user_session_id;
    }

    /**
     * @param $user_session_id
     * @return null|int Returns null if session not found or has no user
     */
    private static function getSessionUserIdBySessionId($user_session_id)
    {
        $user_id = \OLOG\Cache\CacheWrapper::get(self::sessionCacheKey($user_session_id));
        if (!$user_id) {
            //error_log('Auth: no user retrieved for session ' . $user_session_id);
            return null;
        }
        //обновляем куку и сессию в мемекеше, от момента последнегно запроса к сайту
        self::updateUserSession($user_id, $user_session_id);
        return $user_id;
    }

    public static function logout()
    {
        $user_session_id = self::getSessionIdFromCookie();
        if ($user_session_id) {
            self::clearUserSession($user_session_id);
        }

        if( AuthConfig::getAfterLogoutCallbackClassName()){
            \OLOG\CheckClassInterfaces::exceptionIfClassNotImplementsInterface(AuthConfig::getAfterLogoutCallbackClassName(), InterfaceAfterLogoutCallback::class);
            $events_class = AuthConfig::getAfterLogoutCallbackClassName();
            $events_class::afterLogoutCallback();
        }
    }

    public static function clearUserSession($user_session_id)
    {
        self::clearAuthCookie();
        self::removeUserFromAuthCache($user_session_id);
    }

    public static function clearAuthCookie()
    {
        $cookie_name = self::sessionCookieName();
        $cookie_domain = self::sessionCookieDomain();
        setcookie($cookie_name, "", 1000, '/', $cookie_domain, false, true);
    }

    public static function removeUserFromAuthCache($user_session_id)
    {
        \OLOG\Cache\CacheWrapper::delete(self::sessionCacheKey($user_session_id));
    }

    public static function startUserSession($user_id)
    {
        $user_obj = User::factory($user_id, false);
        if (is_null($user_obj)) {
            throw new \Exception('User not found. Can`t start session!');
        }
        $user_session_id = uniqid('as_', true);
        self::updateUserSession($user_id, $user_session_id);
    }

    public static function updateUserSession($user_id, $user_session_id)
    {
        self::storeUserSessionId($user_id, $user_session_id);
        self::setAuthCookieValueBySessionId($user_session_id);
    }

    public static function storeUserSessionId($user_id, $user_session_id)
    {
        $stored = \OLOG\Cache\CacheWrapper::set(self::sessionCacheKey($user_session_id), $user_id, self::SESSION_LIFETIME_SECONDS);
        return $stored;
    }

    public static function setAuthCookieValueBySessionId($user_session_id)
    {
        $cookie_name = self::sessionCookieName();
        $cookie_domain = self::sessionCookieDomain();
        setcookie($cookie_name, $user_session_id, time() + self::SESSION_LIFETIME_SECONDS, '/', $cookie_domain, false, true);
    }

    public static function sessionCookieDomain()
    {
        //return \OLOG\ConfWrapper::value('auth.session_id_cookie_domain', null);
        return AuthConfig::getSessionIdCookieDomain();
    }

    /**
     * @param $login
     * @param $password_from_form
     * @return null
     */
    public static function getUserIdByCredentials($login, $password_from_form)
    {
        $data = \OLOG\DB\DBWrapper::readObject(
            \OLOG\Auth\AuthConstants::DB_NAME_PHPAUTH,
            'SELECT id, password_hash FROM ' . User::DB_TABLE_NAME . ' WHERE login = ?',
            array($login)
        );

        if ($data === false) {
            return null;
        }

        $password_check_result = password_verify($password_from_form, $data->password_hash);

        if (!$password_check_result) {
            return null;
        }

        return $data->id;
    }

    /**
     * @param $requested_permissions_arr
     * @return bool
     */
    static public function currentUserHasAnyOfPermissions($requested_permissions_arr)
    {
        $auth_cookie_name = AuthConfig::getFullAccessCookieName();

        if ($auth_cookie_name) {
            if (isset($_COOKIE[$auth_cookie_name])) {
                return true;
            }
        }

        $current_user_id = self::currentUserId();
        if (!$current_user_id) {
            return false;
        }

        $current_user_obj = User::factory($current_user_id);
        return $current_user_obj->hasAnyOfPermissions($requested_permissions_arr);
    }
}
