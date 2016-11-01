<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Auth;
use OLOG\Auth\Group;
use OLOG\Auth\Operator;
use OLOG\Auth\OwnerCheck;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\Auth\UserToGroup;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormInvisibleRow;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetReferenceAjax;
use OLOG\Exits;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;
use OLOG\Layouts\InterfaceTopActionObj;

class GroupEditAction extends AuthAdminActionsBaseProxy implements
    InterfaceAction,
    InterfaceTopActionObj,
    InterfacePageTitle
{
    private $group_id;

    public function pageTitle()
    {
        return 'Группа ' . $this->group_id;
    }

    public function topActionObj()
    {
        return new GroupsListAction();
    }

    public function __construct($group_id)
    {
        $this->setGroupId($group_id);
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * @param mixed $group_id
     */
    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;
    }


    static public function urlMask()
    {
        return '/admin/auth/group/(\d+)';
    }

    public function url()
    {
        return '/admin/auth/group/' . $this->getGroupId();
    }

    public function action()
    {
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS])
        );

        $group_obj = Group::factory($this->getGroupId(), false);
        \OLOG\Exits::exit404If(!$group_obj);

        Exits::exit403If(
            !OwnerCheck::currentUserOwnsObj($group_obj)
        );

        $html = '';

        $html .= CRUDForm::html(
            $group_obj,
            [
                new CRUDFormRow(
                    'Title',
                    new CRUDFormWidgetInput(Group::_TITLE)
                )
            ]
        );

        $html .= self::adminParamsForm($this->group_id);
        $html .= self::usersInGroupTable($this->group_id);

        AdminLayoutSelector::render($html, $this);
    }

    /**
     * Владельца пока показывает только пользователям с полным доступом.
     * @param $group_id
     * @return string
     */
    static public function adminParamsForm($group_id)
    {
        /** @var User $current_user_obj */
        $current_user_obj = Auth::currentUserObj();
        if (!$current_user_obj) {
            return '';
        }

        if (!$current_user_obj->getHasFullAccess()) {
            return '';
        }

        $html = '';

        $html .= '<h2>Владельцы</h2>';

        $group_obj = Group::factory($group_id);
        $html .= CRUDForm::html(
            $group_obj,
            [
                new CRUDFormRow(
                    'Owner user',
                    new CRUDFormWidgetInput(User::_OWNER_USER_ID, true)
                ),
                new CRUDFormRow(
                    'Owner group',
                    new CRUDFormWidgetInput(User::_OWNER_GROUP_ID, true)
                )
            ]
        );

        return $html;
    }

    public static function usersInGroupTable($group_id)
    {
        if (!Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS])) {
            return '';
        }

        $html = '<h2>Пользователи в группе</h2>';

        $new_user_to_group_obj = new UserToGroup();
        $new_user_to_group_obj->setGroupId($group_id);

        $html .= \OLOG\CRUD\CRUDTable::html(
            UserToGroup::class,
            CRUDForm::html(
                $new_user_to_group_obj,
                [
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(UserToGroup::_GROUP_ID)),
                    new CRUDFormRow('Пользовтель',
                        new CRUDFormWidgetReferenceAjax(UserToGroup::_USER_ID, User::class, User::_LOGIN, (new UsersListAjaxAction())->url(), (new UserEditAction('REFERENCED_ID'))->url(), true))
                ]
            ),
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    'Логин',
                    new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{' . User::class . '.{this->' . UserToGroup::_USER_ID . '}->' . User::_LOGIN . '}', (new UserEditAction('{this->' . UserToGroup::_USER_ID . '}'))->url())
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Удалить',
                    new \OLOG\CRUD\CRUDTableWidgetDelete()
                )
            ],
            [
                new \OLOG\CRUD\CRUDTableFilterEqualInvisible(UserToGroup::_GROUP_ID, $group_id)
            ]
        );

        return $html;
    }
}