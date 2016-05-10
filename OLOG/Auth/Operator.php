<?php

namespace OLOG\Auth;

class Operator implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave,
    \OLOG\Model\InterfaceDelete
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecord;
    use \OLOG\Model\ProtectProperties;

    const DB_ID = 'db_phpauth';
    const DB_TABLE_NAME = 'olog_auth_operator';

    protected $created_at_ts; // initialized by constructor
    protected $title = "";
    protected $user_id;
    protected $id;

    static public function getIdsArrForUserIdByCreatedAtDesc($value){
        $ids_arr = \OLOG\DB\DBWrapper::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' where user_id = ? order by created_at_ts desc',
            array($value)
        );
        return $ids_arr;
    }

    public function getUserId(){
        return $this->user_id;
    }

    public function setUserId($value){
        $this->user_id = $value;
    }


    public function getTitle(){
        return $this->title;
    }

    public function setTitle($value){
        $this->title = $value;
    }


    static public function currentOperatorHasAnyOfPermissions($requested_permissions_arr){
        if (!isset($_COOKIE['php_auth'])){
            return false;
        }

        return true; // TODO: todo
    }

    static public function getAllIdsArrByCreatedAtDesc(){
        $ids_arr = \OLOG\DB\DBWrapper::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' order by created_at_ts desc'
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
     * @param string $title
     */
    public function setCreatedAtTs($title)
    {
        $this->created_at_ts = $title;
    }
}