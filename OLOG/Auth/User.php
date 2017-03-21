<?php

namespace OLOG\Auth;

use OLOG\FullObjectId;
use OLOG\Logger\Entry;

class User implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave,
    \OLOG\Model\InterfaceDelete,
    InterfaceOwner
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecordTrait;
    use \OLOG\Model\ProtectPropertiesTrait;

    const DB_ID = 'db_phpauth';
    const DB_TABLE_NAME = 'olog_auth_user';

    protected $created_at_ts; // initialized by constructor
    protected $login;
    protected $password_hash = "";
    protected $description;
    const _OWNER_USER_ID = 'owner_user_id';
    const _LOGIN = 'login';
    protected $owner_user_id;
    const _OWNER_GROUP_ID = 'owner_group_id';
    protected $owner_group_id;
    const _HAS_FULL_ACCESS = 'has_full_access';
    protected $has_full_access = 0;
    const _PRIMARY_GROUP_ID = 'primary_group_id';
    protected $primary_group_id;
    protected $id;

    /**
     * @param $exception_if_not_found
     * @return $this|null
     * @throws \Exception
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

    public function beforeSave()
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
        $ids_arr = \OLOG\DB\DBWrapper::readColumn(
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

    public function afterSave()
    {
        $this->removeFromFactoryCache();
        $this->writeToLog(__CLASS__ . '::' . __FUNCTION__);
    }

    public function afterDelete()
    {
        $this->removeFromFactoryCache();
        $this->writeToLog(__CLASS__ . '::' . __FUNCTION__);
    }

    public function writeToLog($method_name)
    {
        Entry::logObjectAndId(
            $this->getLoggerPresentation(),
            FullObjectId::getFullObjectId($this),
            $method_name,
            FullObjectId::getFullObjectId(Auth::currentUserObj()));
    }

    public function getLoggerPresentation()
    {
        $presenter_obj = (object)get_object_vars($this);

        $user_to_group_ids_arr = UserToGroup::getIdsArrForUserIdByCreatedAtDesc($this->getId(), 0, 1000);
        foreach ($user_to_group_ids_arr as $user_to_group_id) {
            $presenter_obj->_user_to_group_arr[] = UserToGroup::factory($user_to_group_id);
        }

        $permission_to_user_ids_arr = PermissionToUser::getIdsArrForUserIdByCreatedAtDesc($this->getId(), 0, 1000);
        foreach ($permission_to_user_ids_arr as $permission_to_user_id) {
            $presenter_obj->_permission_to_user_arr[] = PermissionToUser::factory($permission_to_user_id);
        }

        return $presenter_obj;
    }
}
