<?php

namespace OLOG\Auth\Pages;

use OLOG\Auth\Auth;
use OLOG\Auth\AuthConfig;
use OLOG\Auth\User;
use OLOG\BT\Layout;
use OLOG\BT\LayoutBootstrap;
use OLOG\POSTAccess;
use OLOG\Sanitize;
use OLOG\Url;

class LoginAction
{
    static public function getUrl()
    {
        return '/auth/login';
    }

    public function action()
    {
        $user_id = Auth::currentUserId();
        if ($user_id) {
            $html = LoginTemplate::getContent('Пользователь уже авторизован', false);
            LayoutBootstrap::render($html);
            return;
        }

        if (!array_key_exists('login', $_POST) && !array_key_exists('password', $_POST)) {
            $content = LoginTemplate::getContent();
            LayoutBootstrap::render($content);
            return;
        }

        /*
            $is_ip_Banned = UMSHelper::checkBanByCurrentIP();
            if ($is_ip_Banned) {
                $content = UMSSignonTemplate::getContent('Ваш вход заблокирован');
                UMSLayoutTemplate::render("Авторизация", $content);
                return;
            }
        */

        $login = POSTAccess::getOptionalPostValue('login');
        $password = POSTAccess::getOptionalPostValue('password');
        $user_id = Auth::getUserIdByCredentials($login, $password);

        if (!$user_id || ($password == "")) {
            $content = LoginTemplate::getContent('Неправильный адрес или пароль');
            LayoutBootstrap::render($content);
            return;
        }

        $user_obj = User::factory($user_id);

        /*
        if ($user_obj->isBanned()) {
            $content = UMSSignonTemplate::getContent('Ваш аккаунт забанен');
            UMSLayoutTemplate::render("Авторизация", $content);
            return;
        }

        if (!$user_obj->getEmailIsConfirmed()) {
            $resend_activation_main_url = UMSResendUMSEmailActivationAction::getUrl($user_obj->getId());
            $content = UMSSignonTemplate::getContent('Ваша учетная запись не активирована.<br><a href="' . $resend_activation_main_url . '">Отправить ссылку повторно</a>');
            UMSLayoutTemplate::render("Авторизация", $content);
            return;
        }
        */

        Auth::startUserSession($user_obj->getId());

        // set extra cookies
        if (!empty(AuthConfig::getExtraCookiesArr())){
            $extra_cookies_arr = AuthConfig::getExtraCookiesArr();

            foreach ($extra_cookies_arr as $cookie_name => $cookie_value){
                setcookie($cookie_name, $cookie_value, time() + Auth::SESSION_LIFETIME_SECONDS, '/', Auth::sessionCookieDomain(), false, true);
            }
        }

        $redirect = '/';
        $success_redirect_url = POSTAccess::getOptionalPostValue('success_redirect_url', '');
        if ($success_redirect_url != ''){
            $redirect = $success_redirect_url;
        }

        \OLOG\Redirects::redirect($redirect);
    }
}