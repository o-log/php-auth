<?php

namespace OLOG\Auth;

use OLOG\ConfWrapper;

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
        $auth_cookie_name = ConfWrapper::value('php_auth.full_access_cookie_name');

        if ($auth_cookie_name) {
            if (isset($_COOKIE[$auth_cookie_name])) {
                return true;
            }
        }

        $current_user_id = Auth::currentUserId();
        if (!$current_user_id){
            return false;
        }

        $current_operator_ids_arr = Operator::getIdsArrForUserIdByCreatedAtDesc($current_user_id);
        if (empty($current_operator_ids_arr)){
            return false;
        }

        $current_operator_id = $current_operator_ids_arr[0];

        $operator_permissions_ids_arr = OperatorPermission::getIdsArrForOperatorIdByCreatedAtDesc($current_operator_id);

        foreach ($operator_permissions_ids_arr as $operator_permission_id){
            $operator_permission_obj = OperatorPermission::factory($operator_permission_id);
            $permission_id = $operator_permission_obj->getPermissionId();
            $permission_obj = Permission::factory($permission_id);
            if (in_array($permission_obj->getTitle(), $requested_permissions_arr)){
                return true;
            }
        }

        return false;
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