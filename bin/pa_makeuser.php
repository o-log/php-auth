<?php
declare(strict_types=1);

// not using shebang - default php path may be not the desired one

require_once 'vendor/autoload.php';

\Config\Config::init();

$login = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';

if ($login == ''){
    echo("You have to pass new user login as first parameter\n");
    exit;
}

$password = isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : '';

if ($password == ''){
    echo("You have to pass new user password as second parameter\n");
    exit;
}

echo "Creating new user\n";

$user = new \OLOG\Auth\User();
$user->setLogin($login);
$user->setPasswordHash(password_hash($password, PASSWORD_BCRYPT));
$user->setHasFullAccess(true);
$user->save();

//echo "Giving new user all permissions\n";
//
//foreach (\OLOG\Auth\Permission::getAllIdsArrByCreatedAtDesc() as $permission_id){
//    $user_permission = new \OLOG\Auth\PermissionToUser();
//    $user_permission->setUserId($user->getId());
//    $user_permission->setPermissionId($permission_id);
//    $user_permission->save();
//}

echo "Making the user owner to himself\n";

$user->setOwnerUserId($user->getId());
$user->save();
