<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth;

use OLOG\Model\ActiveRecordInterface;

class User implements
    ActiveRecordInterface,
    InterfaceOwner
{
    use \OLOG\Model\ActiveRecordTrait;

    const DB_ID = 'space_phpauth';
    const DB_TABLE_NAME = 'olog_auth_user';

    const _CREATED_AT_TS = 'created_at_ts';
    public $created_at_ts; // initialized by constructor
    public $login;
    public $password_hash = "";
    const _DESCRIPTION = 'description';
    public $description;
    const _OWNER_USER_ID = 'owner_user_id';
    const _LOGIN = 'login';
    public $owner_user_id;
    const _OWNER_GROUP_ID = 'owner_group_id';
    public $owner_group_id;
    const _HAS_FULL_ACCESS = 'has_full_access';
    public $has_full_access = 0;
    const _PRIMARY_GROUP_ID = 'primary_group_id';
    public $primary_group_id;
    public $id;

    public function primaryGroup(): ?Group
    {
        return Group::factory($this->primary_group_id, false);
    }

    /**
     * @param $exception_if_not_found
     */
    static public function factoryForCurrentAuthSession($exception_if_not_found) {
        $user_id = Auth::currentUserId();
        if (!$user_id){
            if ($exception_if_not_found){
                throw new \Exception('User not found');
            }

            return null;
        }

        return self::factory($user_id, $exception_if_not_found);
    }

    public function getPrimaryGroupId()
    {
        return $this->primary_group_id;
    }

    public function setPrimaryGroupId($value)
    {
        $this->primary_group_id = $value;
    }

    public function getHasFullAccess()
    {
        return $this->has_full_access;
    }

    public function setHasFullAccess($value)
    {
        $this->has_full_access = $value;
    }

    public function beforeSave(): void
    {
        if (!is_null($this->getPrimaryGroupId())){
            // создаем связь для пользователя с его основной группой, чтобы основная группа участвовала в проверке доступа
            $user_to_group_obj = UserToGroup::factoryForUserIdAndGroupId($this->getId(), $this->getPrimaryGroupId(), false);
            if (!$user_to_group_obj){
                $new_user_to_group_obj = new UserToGroup();
                $new_user_to_group_obj->setUserId($this->getId());
                $new_user_to_group_obj->setGroupId($this->getPrimaryGroupId());
                $new_user_to_group_obj->save();
            }
        }

        OwnerAssign::assignCurrentUserAsOwnerToObj($this);
    }

    public function __construct()
    {
        $this->created_at_ts = time();
    }

    public function getOwnerGroupId()
    {
        return $this->owner_group_id;
    }

    public function setOwnerGroupId($value)
    {
        $this->owner_group_id = $value;
    }

    public function getOwnerUserId()
    {
        return $this->owner_user_id;
    }

    public function setOwnerUserId($value)
    {
        $this->owner_user_id = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getPasswordHash()
    {
        return $this->password_hash;
    }

    public function setPasswordHash($value)
    {
        $this->password_hash = $value;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($value)
    {
        $this->login = $value;
    }

    static public function getAllIdsArrByCreatedAtDesc()
    {
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' order by created_at_ts desc'
        );
        return $ids_arr;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCreatedAtTs()
    {
        return $this->created_at_ts;
    }

    /**
     * @param string $title
     */
    public function setCreatedAtTs($title)
    {
        $this->created_at_ts = $title;
    }

    /**
     * @param $requested_permissions_arr
     * @return bool
     */
    public function hasAnyOfPermissions($requested_permissions_arr)
    {
        if ($this->getHasFullAccess()){
            return true;
        }

        $user_permissions_ids_arr = PermissionToUser::getIdsArrForUserIdByCreatedAtDesc($this->getId());
        foreach ($user_permissions_ids_arr as $permissiontouser_id) {
            $permissiontouser_obj = PermissionToUser::factory($permissiontouser_id);
            $permission_id = $permissiontouser_obj->getPermissionId();
            $permission_obj = Permission::factory($permission_id);
            if (in_array($permission_obj->getTitle(), $requested_permissions_arr)) {
                return true;
            }
        }

        return false;
    }

    public function afterSave(): void
    {
        $this->removeFromFactoryCache();
    }
}
