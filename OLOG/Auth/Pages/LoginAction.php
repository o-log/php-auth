<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth\Pages;

use OLOG\ActionInterface;
use OLOG\Auth\Auth;
use OLOG\Auth\AuthConfig;
use OLOG\Auth\User;
use OLOG\HTML;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\POST;

class LoginAction implements ActionInterface
{
    public function url()
    {
        return AuthConfig::getLoginUrl();
    }

    public function action()
    {
        $user_id = Auth::currentUserId();
        if ($user_id) {
            $html = LoginTemplate::getContent('Пользователь уже авторизован', false);
            AdminLayoutSelector::render($html);
            return;
        }

        if (!array_key_exists('login', $_POST) && !array_key_exists('password', $_POST)) {
            $content = LoginTemplate::getContent();
            AdminLayoutSelector::render($content);
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

        $login = POST::optional('login');
        $password = POST::optional('password');
        $user_id = Auth::getUserIdByCredentials($login, $password);

        if (!$user_id || ($password == "")) {
            $content = LoginTemplate::getContent('Неправильный адрес или пароль');
            AdminLayoutSelector::render($content);
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

        //ExtraCookiesLib::setExtraCookies();

        $redirect = POST::optional('success_redirect_url', '');

        if ($redirect == ''){
            $redirect = AuthConfig::getDefaultRedirectUrlAfterSuccessfulLogin();
        }

        if ($redirect == ''){
            $redirect = '/';
        }

        \OLOG\Redirects::redirect(HTML::url($redirect));
    }
}
