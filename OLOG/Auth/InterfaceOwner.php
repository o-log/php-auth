<?php

namespace OLOG\Auth;

interface InterfaceOwner
{
    public function getOwnerUserId();
    public function setOwnerUserId($value);
    public function getOwnerGroupId();
    public function setOwnerGroupId($value);
}