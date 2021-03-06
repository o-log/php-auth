<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth\Admin;

use OLOG\Auth\Auth;
use OLOG\Auth\Group;
use OLOG\Auth\OwnerCheck;
use OLOG\Auth\AuthPermissions;
use OLOG\Auth\User;
use OLOG\Auth\UserToGroup;
use OLOG\CRUD\CForm;
use OLOG\CRUD\CTable;
use OLOG\CRUD\FGroup;
use OLOG\CRUD\FGroupHidden;
use OLOG\CRUD\FRow;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\FWReferenceAjax;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFEqualHidden;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWTextWithLink;
use OLOG\Exits;
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
        /*
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS])
        );
        */
        Auth::check([AuthPermissions::PERMISSION_PHPAUTH_MANAGE_GROUPS]);

        $group_obj = Group::factory($this->getGroupId());

        Exits::exit403If(
            !OwnerCheck::currentUserOwnsObj($group_obj)
        );

        $html = '';

        $html .= CForm::html(
            $group_obj,
            [
                new FGroup(
                    'Название',
                    new FWInput(Group::_TITLE)
                )
            ]
        );

        $html .= self::adminParamsForm($this->group_id);
        $html .= self::usersInGroupTable($this->group_id);

        $this->renderInLayout($html);
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
        $html .= CForm::html(
            $group_obj,
            [
                new FGroup(
                    'Пользователь',
                    new FWInput(User::_OWNER_USER_ID, true)
                ),
                new FGroup(
                    'Группа',
                    new FWInput(User::_OWNER_GROUP_ID, true)
                )
            ]
        );

        return $html;
    }

    public static function usersInGroupTable($group_id)
    {
        if (!Auth::currentUserHasAnyOfPermissions([AuthPermissions::PERMISSION_PHPAUTH_MANAGE_USERS])) {
            return '';
        }


        $html = '';
        //$html = '<h4 class="text-muted">Пользователи в группе</h4>';

        $new_user_to_group_obj = new UserToGroup();
        $new_user_to_group_obj->setGroupId($group_id);

        $html .= CTable::html(
            UserToGroup::class,
            CForm::html(
                $new_user_to_group_obj,
                [
                    new FGroupHidden(new FWInput(UserToGroup::_GROUP_ID)),
                    new FRow('Пользователь',
                        new FWReferenceAjax(
                            UserToGroup::_USER_ID,
                            User::class,
                            User::_LOGIN,
                            (new UsersListAjaxAction())->url(),
                            (new UserEditAction('REFERENCED_ID'))->url(),
                            true
                        )
                    )
                ]
            ),
            [
                new TCol(
                    '',
                    new TWTextWithLink(
                        //'{' . User::class . '.{this->' . UserToGroup::_USER_ID . '}->' . User::_LOGIN . '}',
                        function(UserToGroup $utg){
                            return $utg->user()->getLogin();
                        },
                        function(UserToGroup $utg){
                            //(new UserEditAction('{this->' . UserToGroup::_USER_ID . '}'))->url();
                            return (new UserEditAction($utg->getUserId()))->url();
                        }
                    )
                ),
                new TCol(
                    '',
                    new TWDelete()
                )
            ],
            [
                new TFEqualHidden(UserToGroup::_GROUP_ID, $group_id)
            ],
            '',
            'sdgkl987sdfg',
            'Пользователи в группе'
        );

        return $html;
    }
}
