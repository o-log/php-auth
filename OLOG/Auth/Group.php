<?php

namespace OLOG\Auth;

use OLOG\FullObjectId;
use OLOG\Logger\Entry;

class Group implements
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
    const DB_TABLE_NAME = 'olog_auth_group';

    const _CREATED_AT_TS = 'created_at_ts';
    protected $created_at_ts; // initialized by constructor
    const _TITLE = 'title';
    protected $title = "";
    const _OWNER_USER_ID = 'owner_user_id';
    protected $owner_user_id;
    const _OWNER_GROUP_ID = 'owner_group_id';
    protected $owner_group_id;
    const _ID = 'id';
    protected $id;

    public function getOwnerGroupId(){
        return $this->owner_group_id;
    }

    public function setOwnerGroupId($value){
        $this->owner_group_id = $value;
    }

    public function getOwnerUserId(){
        return $this->owner_user_id;
    }

    public function setOwnerUserId($value){
        $this->owner_user_id = $value;
    }

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($value){
        $this->title = $value;
    }

    public function beforeSave()
    {
        OwnerAssign::assignCurrentUserAsOwnerToObj($this);
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
        // Пользовтелей в группе может быть много, поэутому принадлежность к группе логируем на уровне пользователя, не группы
        Entry::logObjectAndId(
            $this,
            FullObjectId::getFullObjectId($this),
            $method_name,
            FullObjectId::getFullObjectId(Auth::currentUserObj()));
    }
}