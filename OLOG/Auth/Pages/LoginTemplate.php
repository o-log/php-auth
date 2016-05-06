<?php

namespace OLOG\Auth\Pages;

class LoginTemplate
{
    public static function getContent($message, $message_type = 'danger')
    {
        ob_start();
        self::render($message, $message_type);
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    static public function render($message, $message_type = 'danger')
    {

        /*
        ?>

        <h1 style="font-size: 22px;" class="text-center">Авторизация</h1>
        <?php
        $current_user_obj = \Sportbox\UMS\UMSHelper::getCurrentUserObj();
        if ($current_user_obj) {
            $user_nickname = $current_user_obj->getName();
            ?>
            <p>Вы уже авторизованы на портале под
                псевдонимом <?php echo \Sportbox\Helpers::check_plain($user_nickname); ?></p>
            <p>Если вы хотите использовать другой аккаунт, Вам необходимо <a
                    href="<?= UMSLogoutAction::getUrl()?>">выйти</a> и
                авторизоваться ещё раз</p>
            <?php
        } else {
        */
            ?>

            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?> width-370" role="alert"><?php echo $message; ?></div>
            <?php endif; ?>
            <form action="" class="form-horizontal form-width-370" method="post" data-toggle="validator">
                <div class="form-group">
                    <div class="">
                        <input type="text" maxlength="100" class="form-control" id="auth-mail"
                               title="Введите Ваш электронный адрес"
                               placeholder="Эл. почта"
                               name="login" data-error=" ">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="">
                        <input type="password" maxlength="100" class="form-control" id="auth-password" name="password"
                               required data-minlength="6"
                               title="Введите пароль"
                               placeholder="Пароль"
                               data-minlength-error="Пароль должен быть длиннее 5-и символов"
                               data-error=" ">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="">
                        <label>
                            <input name="remember" id="reg-confidentiality" type="checkbox"/> Запомнить меня
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="">
                        <input class="btn btn-primary form-control" name="auth" value="Войти" type="submit"/>
                    </div>
                </div>
            </form>

            <div class="bottom_links">
                <a href="#">Регистрация</a>
                <a href="#">Напомнить пароль</a>
            </div>

            <script type="text/javascript">
                <!--//<![CDATA[
                if (document.getElementById("auth-mail")) {
                    document.getElementById("auth-mail").focus();
                }
                //]]>-->
            </script>
        <?php //}
    }
}
