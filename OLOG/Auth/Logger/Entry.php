<?php

namespace OLOG\Auth\Logger;

use OLOG\FullObjectId;

class Entry implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave,
    \OLOG\Model\InterfaceDelete
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecordTrait;
    use \OLOG\Model\ProtectPropertiesTrait;

    const DB_ID = 'db_phpauth';
    const DB_TABLE_NAME = 'olog_auth_logger_entry';

    const _USER_FULLID = 'user_fullid';
    const _OBJECT_FULLID = 'object_fullid';
    const _SERIALIZED_OBJECT = 'serialized_object';
    const _ID = 'id';

    protected $created_at_ts; // initialized by constructor
    protected $user_fullid;
    protected $object_fullid;
    protected $serialized_object;
    protected $user_ip;
    protected $comment;
    protected $id;

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($value)
    {
        $this->comment = $value;
    }

    public function getUserIp()
    {
        return $this->user_ip;
    }

    public function setUserIp($value)
    {
        $this->user_ip = $value;
    }


    public function getSerializedObject()
    {
        return $this->serialized_object;
    }

    public function setSerializedObject($value)
    {
        $this->serialized_object = $value;
    }


    static public function getIdsArrForObjectFullidByCreatedAtDesc($value, $offset = 0, $page_size = 30)
    {
        if (is_null($value)) {
            return \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' where ' . self::_OBJECT_FULLID . ' is null order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
            );
        } else {
            return \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' where ' . self::_OBJECT_FULLID . ' = ? order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset),
                array($value)
            );
        }
    }


    public function getObjectFullid()
    {
        return $this->object_fullid;
    }

    public function setObjectFullid($value)
    {
        $this->object_fullid = $value;
    }


    static public function getIdsArrForUserFullidByCreatedAtDesc($value, $offset = 0, $page_size = 30)
    {
        if (is_null($value)) {
            return \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' where ' . self::_USER_FULLID . ' is null order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
            );
        } else {
            return \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' where ' . self::_USER_FULLID . ' = ? order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset),
                array($value)
            );
        }
    }


    public function getUserFullid()
    {
        return $this->user_fullid;
    }

    public function setUserFullid($value)
    {
        $this->user_fullid = $value;
    }

    /**
     * Сохранение объекта
     * Каждый сохраняемый объект запоминаем. если запрашивается сохранение с таким же object_fullid
     * то смотрим есть ли изменения по сравнению с уже сохраненным.
     * Если нет то не сохраняем. если есть то переписываем уже сохраненый
     *
     * @param $object
     * @param $object_fullid
     * @param $comment
     * @param $user_fullid
     */
    static function logObjectAndId($object, $object_fullid, $comment, $user_fullid)
    {
        $ip_address = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : '';
        static $saved_entries_arr = [];
        if (!array_key_exists($object_fullid, $saved_entries_arr)) {
            $new_entry_obj = new Entry();
            $new_entry_obj->setUserIp($ip_address);
            $new_entry_obj->setUserFullid($user_fullid);
            $new_entry_obj->setObjectFullid($object_fullid);
            $new_entry_obj->setSerializedObject(serialize($object));
            $new_entry_obj->setComment($comment);
            $new_entry_obj->save();
            $saved_entries_arr[$object_fullid] = $new_entry_obj;
        } else {
            $saved_entry_obj = $saved_entries_arr[$object_fullid];
            $serialized_object = serialize($object);
            if ($serialized_object != $saved_entry_obj->getSerializedObject()) {
                $saved_entry_obj->setSerializedObject($serialized_object);
                $saved_entry_obj->save();
                $saved_entries_arr[$object_fullid] = $saved_entry_obj;
            }
        }
    }

    static public function logObjectEvent($object, $comment, $user_fullid)
    {
        self::logObjectAndId($object, FullObjectId::getFullObjectId($object), $comment, $user_fullid);
    }

    static public function getAllIdsArrByCreatedAtDesc($offset = 0, $page_size = 30)
    {
        $ids_arr = \OLOG\DB\DBWrapper::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
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
     * @param string $timestamp
     */
    public function setCreatedAtTs($timestamp)
    {
        $this->created_at_ts = $timestamp;
    }

}