<?php

namespace OLOG\Auth;

use OLOG\Model\ActiveRecordInterface;

class Permission implements
    ActiveRecordInterface
{
    use \OLOG\Model\ActiveRecordTrait;
    use \OLOG\Model\ProtectPropertiesTrait;

    const DB_ID = 'space_phpauth';
    const DB_TABLE_NAME = 'olog_auth_permission';

    protected $created_at_ts; // initialized by constructor
    protected $title;
    protected $id;

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($value){
        $this->title = $value;
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
}