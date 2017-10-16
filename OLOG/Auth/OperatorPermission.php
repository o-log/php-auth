<?php

namespace OLOG\Auth;

use OLOG\Model\ActiveRecordInterface;

class OperatorPermission implements
    ActiveRecordInterface
{
    use \OLOG\Model\ActiveRecordTrait;
    use \OLOG\Model\ProtectPropertiesTrait;

    const DB_ID = 'space_phpauth';
    const DB_TABLE_NAME = 'olog_auth_operatorpermission';

    protected $created_at_ts; // initialized by constructor
    protected $operator_id;
    protected $permission_id;
    protected $id;

    public function getPermissionId(){
        return $this->permission_id;
    }

    public function setPermissionId($value){
        $this->permission_id = $value;
    }


    static public function getIdsArrForOperatorIdByCreatedAtDesc($value){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' where operator_id = ? order by created_at_ts desc',
            array($value)
        );
        return $ids_arr;
    }

    public function getOperatorId(){
        return $this->operator_id;
    }

    public function setOperatorId($value){
        $this->operator_id = $value;
    }


    static public function getAllIdsArrByCreatedAtDesc(){
        $ids_arr = \OLOG\DB\DB::readColumn(
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

    /**
     * @param $operator_id
     * @return array
     */
    public static function getPermissionIdsArrForOperatorId($operator_id) {
        return \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select permission_id from ' . self::DB_TABLE_NAME . ' where operator_id = ?',
            array($operator_id)
        );
    }
}