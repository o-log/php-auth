<?php

namespace OLOG\Auth\Admin;

use OLOG\ActionInterface;
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
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\Exits;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\PageTitleInterface;
use OLOG\Layouts\TopActionObjInterface;
use OLOG\MaskActionInterface;

class GroupEditAction extends AuthAdminActionsBaseProxy implements
    MaskActionInterface,
    TopActionObjInterface,
    PageTitleInterface
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


    static public function mask()
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

        $group_obj = Group::factory($this->getGroupId());

        Exits::exit403If(
            !OwnerCheck::currentUserOwnsObj($group_obj)
        );

        $html = '';

        $html .= CRUDForm::html(
            $group_obj,
            [
                new CRUDFormRow(
                    'Название',
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
                    'Пользователь',
                    new CRUDFormWidgetInput(User::_OWNER_USER_ID, true)
                ),
                new CRUDFormRow(
                    'Группа',
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

        $html .= CRUDTable::html(
            UserToGroup::class,
            CRUDForm::html(
                $new_user_to_group_obj,
                [
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(UserToGroup::_GROUP_ID)),
                    new CRUDFormRow('Пользователь',
                        new CRUDFormWidgetReferenceAjax(UserToGroup::_USER_ID, User::class, User::_LOGIN, (new UsersListAjaxAction())->url(), (new UserEditAction('REFERENCED_ID'))->url(), true))
                ]
            ),
            [
                new CRUDTableColumn(
                    'Логин',
                    new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{' . User::class . '.{this->' . UserToGroup::_USER_ID . '}->' . User::_LOGIN . '}', (new UserEditAction('{this->' . UserToGroup::_USER_ID . '}'))->url())
                ),
                new CRUDTableColumn(
                    'Удалить',
                    new CRUDTableWidgetDelete()
                )
            ],
            [
                new CRUDTableFilterEqualInvisible(UserToGroup::_GROUP_ID, $group_id)
            ]
        );

        return $html;
    }
}