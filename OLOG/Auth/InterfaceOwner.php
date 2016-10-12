<?php

namespace OLOG\Auth;

interface InterfaceOwner
{
    public function getOwnerUserId();
    public function setOwnerUserId();
    public function getOwnerGroupId();
    public function setOwnerGroupId();
}