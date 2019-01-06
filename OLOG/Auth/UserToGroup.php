<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth;

use OLOG\Model\ActiveRecordInterface;

class UserToGroup implements
    ActiveRecordInterface
{
    use \OLOG\Model\ActiveRecordTrait;

    const DB_ID = 'space_phpauth';
    const DB_TABLE_NAME = 'olog_auth_usertogroup';

    const _CREATED_AT_TS = 'created_at_ts';
    public $created_at_ts; // initialized by constructor
    const _USER_ID = 'user_id';
    public $user_id;
    const _GROUP_ID = 'group_id';
    public $group_id;
    const _ID = 'id';
    public $id;

    public function group(): ?Group
    {
        return Group::factory($this->group_id);
    }

    public function user(): ?User
    {
        return User::factory($this->user_id);
    }

    static public function factoryForUserIdAndGroupId($user_id, $group_id, $exception_if_not_loaded = true)
    {
        $obj = null;
        $ids_arr_for_userid_and_group_id = self::getIdsArrForUserIdAndGroupId($user_id, $group_id);

        if (count($ids_arr_for_userid_and_group_id) == 1){
            $obj = self::factory($ids_arr_for_userid_and_group_id[0], false);
        }

        if (is_null($obj) && $exception_if_not_loaded){
            throw new \Exception('Nothing loaded');
        }

        return $obj;
    }

    static public function getIdsArrForUserIdAndGroupId($user_id, $group_id){
        return \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' where ' . self::_USER_ID . ' = ? and ' . self::_GROUP_ID . ' = ?',
            [$user_id, $group_id]
        );
    }

    static public function getIdsArrForUserIdByCreatedAtDesc($value, $offset = 0, $page_size = 1000){
        if (is_null($value)) {
            return \OLOG\DB\DB::readColumn(
                self::DB_ID,
                'select id from ' . self::DB_TABLE_NAME . ' where ' . self::_USER_ID . ' is null order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
            );
        } else {
            return \OLOG\DB\DB::readColumn(
                self::DB_ID,
                'select id from ' . self::DB_TABLE_NAME . ' where ' . self::_USER_ID . ' = ? order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset),
                array($value)
            );
        }
    }

    public function getGroupId(){
        return $this->group_id;
    }

    public function setGroupId($value){
        $this->group_id = $value;
    }



    public function getUserId(){
        return $this->user_id;
    }

    public function setUserId($value){
        $this->user_id = $value;
    }

    public function canDelete(&$message): bool
    {
        // запрещаем удалять связь пользователя с его основной группой

        $user_obj = User::factory($this->getUserId());
        if (is_null($user_obj->getPrimaryGroupId())){
            return true;
        }

        if ($this->getGroupId() == $user_obj->getPrimaryGroupId()){
            $message = 'Запрещено отвязывать от пользователя его группу по умолчанию';
            return false;
        }

        return true;
    }

    static public function getAllIdsArrByCreatedAtDesc($offset = 0, $page_size = 30){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' order by ' . self::_CREATED_AT_TS . ' desc limit ' . intval($page_size) . ' offset ' . intval($offset)
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
}
