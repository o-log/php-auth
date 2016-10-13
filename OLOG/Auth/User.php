<?php

namespace OLOG\Auth;

use OLOG\Layouts\IntefacePageLogoHtml;

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
    protected $owner_user_id;
    const _OWNER_GROUP_ID = 'owner_group_id';
    protected $owner_group_id;
    protected $id;

    public function __construct(){
        $this->created_at_ts = time();

        $this->setOwnerUserId(Auth::currentUserId());

        // TODO: set new user owner group to current user primary group
    }

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

    public function getDescription(){
        return $this->description;
    }

    public function setDescription($value){
        $this->description = $value;
    }

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