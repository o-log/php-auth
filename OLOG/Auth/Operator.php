<?php

namespace OLOG\Auth;

use OLOG\Model\ActiveRecordInterface;

class Operator implements
    ActiveRecordInterface
{
    use \OLOG\Model\ActiveRecordTrait;
    use \OLOG\Model\ProtectPropertiesTrait;

    const DB_ID = 'space_phpauth';
    const DB_TABLE_NAME = 'olog_auth_operator';
    const _COMMENT = 'description';

    protected $created_at_ts; // initialized by constructor
    protected $title = "";
    protected $user_id;
    protected $description;
    protected $id;

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    static public function getIdsArrForUserIdByCreatedAtDesc($value)
    {
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' where user_id = ? order by created_at_ts desc',
            array($value)
        );
        return $ids_arr;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($value)
    {
        $this->user_id = $value;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($value)
    {
        $this->title = $value;
    }

    static public function currentOperatorHasAnyOfPermissions($requested_permissions_arr)
    {
        $current_user_has_any_of_permissions = Auth::currentUserHasAnyOfPermissions($requested_permissions_arr);
        if($current_user_has_any_of_permissions) {
            return true;
        }

        $current_user_id = Auth::currentUserId();
        if (!$current_user_id) {
            //error_log('Auth: no current user');
            return false;
        }

        // check operator permissions

        $current_operator_ids_arr = Operator::getIdsArrForUserIdByCreatedAtDesc($current_user_id);
        if (empty($current_operator_ids_arr)) {
            //error_log('Auth: no operators for user ' . $current_user_id);
            return false;
        }

        $current_operator_id = $current_operator_ids_arr[0];

        $operator_permissions_ids_arr = OperatorPermission::getIdsArrForOperatorIdByCreatedAtDesc($current_operator_id);

        $assigned_permissions_titles_arr = [];

        foreach ($operator_permissions_ids_arr as $operator_permission_id) {
            $operator_permission_obj = OperatorPermission::factory($operator_permission_id);
            $permission_id = $operator_permission_obj->getPermissionId();
            $permission_obj = Permission::factory($permission_id);
            $assigned_permissions_titles_arr[] = $permission_obj->getTitle();
            if (in_array($permission_obj->getTitle(), $requested_permissions_arr)) {
                return true;
            }
        }

        //error_log('Auth: no permissions for operator ' . $current_operator_id . ' (' . implode(',', $operator_permissions_ids_arr) . ') (' . implode(',', $assigned_permissions_titles_arr) . ') matched requested list: ' . implode(',', $requested_permissions_arr));

        return false;
    }

    static public function getAllIdsArrByCreatedAtDesc()
    {
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' order by created_at_ts desc'
        );
        return $ids_arr;
    }

    public function __construct()
    {
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