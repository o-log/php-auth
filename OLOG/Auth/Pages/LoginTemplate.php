<?php

namespace OLOG\Auth\Pages;

class LoginTemplate
{
    public static function getContent($message = '', $show_form = true)
    {
        ob_start();
        self::render($message, $show_form);
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    static public function render($message = '', $show_form = true)
    {
        $message_type = 'danger';
        ?>
        <style>
            body {
                padding-top: 40px;
                padding-bottom: 40px;
                background-color: #eee;
            }

            .form-signin {
                max-width: 330px;
                padding: 15px;
                margin: 0 auto;
            }
            .form-signin .form-signin-heading,
            .form-signin .checkbox {
                margin-bottom: 10px;
            }
            .form-signin .checkbox {
                font-weight: normal;
            }
            .form-signin .form-control {
                position: relative;
                height: auto;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                padding: 10px;
                font-size: 16px;
            }
            .form-signin .form-control:focus {
                z-index: 2;
            }
            .form-signin input[type="password"] {
                margin-bottom: 10px;
                border-top-left-radius: 0;
                border-top-right-radius: 0;
            }
        </style>

        <form class="form-signin" method="post">
            <h2 class="form-signin-heading">Please sign in</h2>
            <?php if ($message){ ?>
                <div class="alert alert-<?= $message_type ?> width-370" role="alert"><?php echo $message; ?></div>
            <?php } ?>

            <?php if ($show_form){ ?>
            <label for="inputEmail" class="sr-only">Email address</label>
            <input style="margin-bottom: -1px; border-bottom-right-radius: 0; border-bottom-left-radius: 0;" name="login" class="form-control" placeholder="login" required autofocus>
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <!--
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="remember-me"> Remember me
                </label>
            </div>
            -->
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>

            <?php } ?>
        </form>
        <?php //}
    }
}
