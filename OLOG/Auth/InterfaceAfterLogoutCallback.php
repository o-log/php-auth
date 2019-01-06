<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth;

interface InterfaceAfterLogoutCallback
{
    static public function afterLogoutCallback();
}
