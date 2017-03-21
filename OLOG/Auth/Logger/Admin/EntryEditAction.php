<?php

namespace OLOG\Auth\Logger\Admin;

use OLOG\Auth\Admin\UserEditAction;
use OLOG\Auth\Operator;
use OLOG\Auth\User;
use OLOG\DB\DBWrapper;
use OLOG\Exits;
use OLOG\HTML;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;
use OLOG\Auth\Logger\Entry;
use OLOG\Auth\Logger\Permissions;

class EntryEditAction extends LoggerAdminActionsBaseProxy implements
    InterfaceAction,
    InterfacePageTitle
{
    protected $entry_id;

    public function __construct($entry_id)
    {
        $this->entry_id = $entry_id;
    }

    public function url()
    {
        return '/admin/logger/entry/' . $this->entry_id;
    }

    static public function urlMask()
    {
        return '/admin/logger/entry/(\d+)';
    }

    public function pageTitle()
    {
        return 'Запись ' . $this->entry_id;
    }

    public function topActionObj()
    {
        $current_record_obj = Entry::factory($this->entry_id);
        return new ObjectEntriesListAction($current_record_obj->getObjectFullid());
    }

    public function action()
    {
        Exits::exit403If(!Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPLOGGER_ACCESS]));
        $html = '';
        $html .= self::renderRecordHead($this->entry_id);
        $html .= self::delta($this->entry_id);
        $html .= self::renderObjectFields($this->entry_id);

        AdminLayoutSelector::render($html, $this);
    }

    static public function delta($current_record_id)
    {
        $html = '';

        $current_record_obj = Entry::factory($current_record_id);

        // находим предыдущую запись лога для этого объекта

        $prev_record_id = DBWrapper::readField(
            Entry::DB_ID,
            "SELECT " . Entry::_ID . " FROM " . Entry::DB_TABLE_NAME . " WHERE " . Entry::_ID . " < ? AND " . Entry::_OBJECT_FULLID . " = ? ORDER BY id DESC LIMIT 1",
            array($current_record_id, $current_record_obj->getObjectFullid())
        );

        if (!$prev_record_id) {
            return '<div>Предыдущая запись истории для этого объекта не найдена.</div>';
        }

        $prev_record_obj = Entry::factory($prev_record_id);

        // определение дельты

        $html .= '<h2>Изменения относительно <a href="' . (new EntryEditAction($prev_record_id))->url() . '">предыдущей версии</a></h2>';

        $current_obj = unserialize($current_record_obj->getSerializedObject());
        $prev_obj = unserialize($prev_record_obj->getSerializedObject());

        $current_record_as_list = self::convertValueToList($current_obj);
        ksort($current_record_as_list); // сортируем для красоты
        $prev_record_as_list = self::convertValueToList($prev_obj);
        ksort($prev_record_as_list); // сортируем для красоты

        $html .= '<table class="table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Поле</th>';
        $html .= '<th>Старое значение</th>';
        $html .= '<th>Новое значение</th>';
        $html .= '</tr>';
        $html .= '</thead>';

        $added_rows = array_diff_key($current_record_as_list, $prev_record_as_list);

        foreach ($added_rows as $k => $v) {
            $html .= '<tr>';
            $html .= '<td><b>' . $k . '</b></td>';
            $html .= '<td style="background-color: #eee;"></td>';
            $html .= '<td>' . self::renderDeltaValue($v) . '</td>';
            $html .= '</tr>';
        }

        $deleted_rows = array_diff_key($prev_record_as_list, $current_record_as_list);

        foreach ($deleted_rows as $k => $v) {
            $html .= '<tr>';
            $html .= '<td><b>' . $k . '</b></td>';
            $html .= '<td>' . self::renderDeltaValue($v) . '</td>';
            $html .= '<td style="background-color: #eee;"></td>';
            $html .= '</tr>';
        }

        foreach ($current_record_as_list as $k => $current_v) {
            if (array_key_exists($k, $prev_record_as_list)) {
                $prev_v = $prev_record_as_list[$k];
                if ($current_v != $prev_v) {
                    $html .= '<tr>';
                    $html .= '<td><b>' . $k . '</b></td>';
                    $html .= '<td>' . self::renderDeltaValue($prev_v) . '</td>';
                    $html .= '<td>' . self::renderDeltaValue($current_v) . '</td>';
                    $html .= '</tr>';
                }
            }
        }

        $html .= '</table>';

        $html .= '<div>Для длинных значений полный текст здесь не приведен, его можно увидеть в полях объекта ниже.</div>';

        return $html;
    }

    static public function renderDeltaValue($v)
    {
        $limit = 300;

        if (strlen($v) < $limit) {
            return $v;
        }

        return mb_substr($v, 0, $limit) . '...';
    }

    static public function getUserNameWithLinkByFullId($user_fullid)
    {
        $user_str = $user_fullid;
        $user_fullid_arr = explode('.', $user_str);
        if (!array_key_exists(1, $user_fullid_arr)) {
            return $user_str;
        }
        $user_obj = User::factory($user_fullid_arr[1], false);
        if (is_null($user_obj)) {
            return $user_str;
        }
        return HTML::a((new UserEditAction($user_obj->getId()))->url(), $user_obj->getLogin());
    }

    static public function renderRecordHead($record_id)
    {
        $record_obj = Entry::factory($record_id);

        $user_str = self::getUserNameWithLinkByFullId($record_obj->getUserFullid());

        return '<dl class="dl-horizontal jumbotron" style="margin-top:20px;padding: 10px;">
	<dt style="padding: 5px 0;">Имя пользователя</dt>
	<dd style="padding: 5px 0;">' . $user_str . '</dd>
    <dt style="padding: 5px 0;">Время изменения</dt>
    <dd style="padding: 5px 0;">' . date('d.m H:i', $record_obj->getCreatedAtTs()) . '</dd>
    <dt style="padding: 5px 0;">IP адрес</dt>
    <dd style="padding: 5px 0;">' . $record_obj->getUserIp() . '</dd>
    <dt style="padding: 5px 0;">Комментарий</dt>
    <dd style="padding: 5px 0;">' . $record_obj->getComment() . '</dd>
    <dt style="padding: 5px 0;">Идентификатор</dt>
    <dd style="padding: 5px 0;">' . $record_obj->getObjectFullid() . '</dd>
</dl>
   ';
    }

    static public function renderObjectFields($record_id)
    {
        $html = '<h2>Все поля объекта</h2>';

        $record_obj = Entry::factory($record_id);

	$record_objs = unserialize($record_obj->getSerializedObject());

        $value_as_list = self::convertValueToList($record_objs);
        ksort($value_as_list); // сортируем для красоты

        //$html .= '<table class="table">';
        $last_path = '';

        foreach ($value_as_list as $path => $value) {
            $path_to_display = $path;

            if (self::getPathWithoutLastElement($last_path) == self::getPathWithoutLastElement($path)) {
                $elems = explode('.', $path);
                $last_elem = array_pop($elems);
                if (count($elems)) {
                    $path_to_display = '<span style="color: #999">' . implode('.', $elems) . '</span>.' . $last_elem;
                }
            }

            /*
            $html .= '<tr>';
            $html .= '<td>' . $path_to_display . '</td>';
            $html .= '<td><pre style="white-space: pre-wrap;">' . $value . '</pre></td>';
            $html .= '</tr>';
            */

            if (strlen($value) > 100) {
                $html .= '<div style="padding: 5px 0px; border-bottom: 1px solid #ddd;">';

                $html .= '<div><b>' . $path_to_display . '</b></div>';
                $html .= '<div><pre style="white-space: pre-wrap;">' . $value . '</pre></div>';
                $html .= '</div>';
            } else {
                $html .= '<div style="padding: 5px 0px; border-bottom: 1px solid #ddd;">';

                $html .= '<span style="padding-right: 50px;"><b>' . $path_to_display . '</b></span>';
                $html .= $value;
                $html .= '</div>';
            }


            $last_path = $path;
        }
        //$html .= '</table>';

        return $html;
    }

    static public function convertValueToList($value_value, $value_path = '')
    {
        if (is_null($value_value)) {
            return array($value_path => '#NULL#');
        }

        if (is_scalar($value_value)) {
            return array($value_path => htmlentities($value_value));
        }

        $value_as_array = null;
        $output_array = array();

        if (is_array($value_value)) {
            $value_as_array = $value_value;
        }

        if (is_object($value_value)) {
            $value_as_array = array();

            foreach ($value_value as $property_name => $property_value) {
                $value_as_array[$property_name] = $property_value;
            }

            $reflect = new \ReflectionClass($value_value);
            $properties = $reflect->getProperties();

            foreach ($properties as $prop_obj) {
                // не показываем статические свойства класса - они не относятся к конкретному объекту (например, это могут быть настройки круда для класса) и в журнале не нужны
                if ($prop_obj->isStatic()) {
                    continue;
                }

                $prop_obj->setAccessible(true);
                $name = $prop_obj->getName();
                $value = $prop_obj->getValue($value_value);
                $value_as_array[$name] = $value;
            }
        }

        if (!is_array($value_as_array)) {
            throw new \Exception('Не удалось привести значение к массиву');
        }

        foreach ($value_as_array as $key => $value) {
            $key_path = $key;
            if ($value_path != '') {
                $key_path = $value_path . '.' . $key;
            }

            $value_output = self::convertValueToList($value, $key_path);
            $output_array = array_merge($output_array, $value_output);
        }

        return $output_array;
    }

    static public function getPathWithoutLastElement($path)
    {
        $elems = explode('.', $path);
        array_pop($elems);
        return implode('.', $elems);
    }


}
