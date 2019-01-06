<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth;

interface InterfaceOwner
{
    public function getOwnerUserId();
    public function setOwnerUserId($value);
    public function getOwnerGroupId();
    public function setOwnerGroupId($value);
}
