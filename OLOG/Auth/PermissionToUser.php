<?php

namespace OLOG\Auth;

use OLOG\Cache\CacheWrapper;

class PermissionToUser implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave,
    \OLOG\Model\InterfaceDelete
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecordTrait;
    use \OLOG\Model\ProtectPropertiesTrait;

    const DB_ID = 'db_phpauth';
    const DB_TABLE_NAME = 'olog_auth_permissiontouser';

    protected $created_at_ts; // initialized by constructor
    protected $user_id;
    protected $permission_id;
    protected $id;

    public function afterDelete()
    {
        $this->removeFromFactoryCache();
        CacheWrapper::delete(self::getCacheKey_getIdsArrForUserIdByCreatedAtDesc($this->getUserId()));
        $user_obj = User::factory($this->getUserId());
        $user_obj->writeToLog(__CLASS__ . '::' . __FUNCTION__);
    }

    public function afterSave()
    {
        $this->removeFromFactoryCache();
        CacheWrapper::delete(self::getCacheKey_getIdsArrForUserIdByCreatedAtDesc($this->getUserId()));
        $user_obj = User::factory($this->getUserId());
        $user_obj->writeToLog(__CLASS__ . '::' . __FUNCTION__);
    }

    static public function getIdsArrForPermissionIdByCreatedAtDesc($value, $offset = 0, $page_size = 30){
        if (is_null($value)) {
            return \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select id from ' . self::DB_TABLE_NAME . ' where permission_id is null order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
            );
        } else {
            return \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select id from ' . self::DB_TABLE_NAME . ' where permission_id = ? order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset),
                array($value)
            );
        }
    }


    public function getPermissionId(){
        return $this->permission_id;
    }

    public function setPermissionId($value){
        $this->permission_id = $value;
    }


    static public function getCacheKey_getIdsArrForUserIdByCreatedAtDesc($user_id){
        $user_id_str = $user_id;
        if (is_null($user_id)){
            $user_id_str = 'null';
        }
        return 'getIdsArrForUserIdByCreatedAtDesc_' . $user_id_str;
    }

    static public function getIdsArrForUserIdByCreatedAtDesc($user_id, $offset = 0, $page_size = 30){

        $cahe_key = self::getCacheKey_getIdsArrForUserIdByCreatedAtDesc($user_id);
        $cached_data = CacheWrapper::get($cahe_key);
        if(is_array($cached_data)){
            return $cached_data;
        }


        if (is_null($user_id)) {
            $ids_arr = \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select id from ' . self::DB_TABLE_NAME . ' where user_id is null order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
            );
        } else {
            $ids_arr = \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select id from ' . self::DB_TABLE_NAME . ' where user_id = ? order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset),
                array($user_id)
            );
        }

        CacheWrapper::set($cahe_key, $ids_arr);
        return $ids_arr;
    }


    public function getUserId(){
        return $this->user_id;
    }

    public function setUserId($value){
        $this->user_id = $value;
    }



    static public function getAllIdsArrByCreatedAtDesc($offset = 0, $page_size = 30){
        $ids_arr = \OLOG\DB\DBWrapper::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
        );
        return $ids_arr;
    }

    public function __construct(){
        $this->created_at_ts = time();
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
     * @param string $timestamp
     */
    public function setCreatedAtTs($timestamp)
    {
        $this->created_at_ts = $timestamp;
    }

    static public function getPermissionIdsArrForUserId($value){
        return \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select permission_id from ' . self::DB_TABLE_NAME . ' where user_id = ?',
                array($value)
            );
    }
}