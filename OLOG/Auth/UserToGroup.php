<?php

namespace OLOG\Auth;

class UserToGroup implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave,
    \OLOG\Model\InterfaceDelete
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecordTrait;
    use \OLOG\Model\ProtectPropertiesTrait;

    const DB_ID = 'db_phpauth';
    const DB_TABLE_NAME = 'olog_auth_usertogroup';

    const _CREATED_AT_TS = 'created_at_ts';
    protected $created_at_ts; // initialized by constructor
    const _USER_ID = 'user_id';
    protected $user_id;
    const _GROUP_ID = 'group_id';
    protected $group_id;
    const _ID = 'id';
    protected $id;

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



    static public function getAllIdsArrByCreatedAtDesc($offset = 0, $page_size = 30){
        $ids_arr = \OLOG\DB\DBWrapper::readColumn(
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