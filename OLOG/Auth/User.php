<?php

namespace OLOG\Auth;

class User implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave,
    \OLOG\Model\InterfaceDelete
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecord;
    use \OLOG\Model\ProtectProperties;

    const DB_ID = 'db_phpauth';
    const DB_TABLE_NAME = 'olog_auth_user';

    protected $created_at_ts; // initialized by constructor
    protected $login = "";
    protected $password_hash = "";
    protected $id;

    public function getPasswordHash(){
        return $this->password_hash;
    }

    public function setPasswordHash($value){
        $this->password_hash = $value;
    }


    public function getLogin(){
        return $this->login;
    }

    public function setLogin($value){
        $this->login = $value;
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