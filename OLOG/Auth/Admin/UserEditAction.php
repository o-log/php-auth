<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth\Admin;

use OLOG\Auth\Auth;
use OLOG\Auth\Group;
use OLOG\Auth\OwnerCheck;
use OLOG\Auth\Permission;
use OLOG\Auth\AuthPermissions;
use OLOG\Auth\PermissionToUser;
use OLOG\Auth\User;
use OLOG\Auth\UserToGroup;
use OLOG\CRUD\CForm;
use OLOG\CRUD\CTable;
use OLOG\CRUD\FGroup;
use OLOG\CRUD\FGroupHidden;
use OLOG\CRUD\FRow;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\FWReferenceAjax;
use OLOG\CRUD\FWTextarea;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFEqualHidden;
use OLOG\CRUD\TFNotInHidden;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWText;
use OLOG\CRUD\TWTextWithLink;
use OLOG\Exits;
use OLOG\Form;
use OLOG\HTML;
use OLOG\Layouts\PageTitleInterface;
use OLOG\Layouts\TopActionObjInterface;
use OLOG\MagnificPopup;
use OLOG\MaskActionInterface;
use OLOG\POST;
use OLOG\Url;

class UserEditAction extends AuthAdminActionsBaseProxy implements
    MaskActionInterface,
    PageTitleInterface,
    TopActionObjInterface
{
    const OPERATION_SET_PASSWORD = 'OPERATION_SET_PASSWORD';
    const FIELD_NAME_PASSWORD = 'password';

    protected $user_id;

    public function topActionObj()
    {
        return new UsersListAction();
    }

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function pageTitle()
    {
        $user = User::factory($this->user_id);
        return $user->getLogin();
    }

    public function url()
    {
        return '/admin/auth/user/' . $this->user_id;
    }

    static public function mask()
    {
        return '/admin/auth/user/(\d+)';
    }

    public function action()
    {
        Auth::check([AuthPermissions::PERMISSION_PHPAUTH_MANAGE_USERS]);

        $user_id = $this->user_id;
        $user_obj = User::factory($user_id);

        Exits::exit403If(
            !OwnerCheck::currentUserOwnsObj($user_obj)
        );

        Form::match(self::OPERATION_SET_PASSWORD, function () use ($user_id) {
            $new_password = POST::optional(self::FIELD_NAME_PASSWORD);
            $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

            $user_obj = User::factory($user_id);
            $user_obj->setPasswordHash($new_password_hash);
            $user_obj->save();
        });

        $html = '';

        $html .= '<div class="row"><div class="col-md-6">';

        $html .= self::commonParamsForm($user_id);
        $html .= self::passwordForm();
        //$html .= self::userOperatorsTable($user_id);
        $html .= self::adminParamsForm($user_id);

        $html .= '</div><div class="col-md-6">';

        if (Auth::currentUserHasAnyOfPermissions([AuthPermissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS])) {
            //$html .= '<h4 class="text-muted">Разрешения <span>' . MagnificPopup::button('jjhgdkshgdsfg3456', 'btn btn-secondary btn-sm', '<i class="fa fa-plus"></i>') . '</span></h4>';
            $html .= HTML::div('', '', function () use ($user_id) {
                $new_permissiontouser_obj = new PermissionToUser();
                $new_permissiontouser_obj->setUserId($user_id);

                //echo CollapsibleWidget::buttonAndCollapse('Неназначенные разрешения', function () use ($user_id) {
                $popup_fn = function () use ($user_id) {
                    $html = '<h4 class="text-muted">Неназначенные пользователю разрешения</h4>';
                    $html .= CTable::html(
                        Permission::class,
                        '',
                        [
                            new TCol(
                                '',
                                new TWTextWithLink(
                                    Permission::_TITLE,
                                    function (Permission $permission) use ($user_id) {
                                        return (new PermissionAddToUserAction($user_id, $permission->id))->url();
                                    }
                                )
                            ),
                            new TCol(
                                '',
                                new TWTextWithLink(
                                    '+',
                                    function(Permission $permission) use ($user_id) {
                                        return (new PermissionAddToUserAction($user_id, $permission->id))->url();
                                    },
                                    'btn btn-secondary btn-sm'
                                )
                            )
                        ],
                        [
                            new TFNotInHidden('id', PermissionToUser::getPermissionIdsArrForUserId($user_id)),
                        ],
                        'title asc',
                        '79687tg8976rt87',
                        '',
                        false,
                        30,
                        true
                    );

                    return $html;
                };

                echo MagnificPopup::popupHtml('jjhgdkshgdsfg3456', $popup_fn());

                echo CTable::html(
                    PermissionToUser::class,
                    '',
                    [
                        new TCol(
                            '',
                            new TWText(
                                //'{' . Permission::class . '.{this->permission_id}->title}'
                                function (PermissionToUser $ptu){
                                    return $ptu->permission()->title;
                                }
                            )
                        ),
                        new TCol(
                            '', new TWDelete()
                        )
                    ],
                    [
                        new TFEqualHidden('user_id', $user_id)
                    ],
                    '',
                    'fasdfrsgxcv',
                    'Разрешения <span class="pull-right">' . MagnificPopup::button('jjhgdkshgdsfg3456', 'btn btn-secondary btn-sm', '<i class="fa fa-plus"></i></span>'),
                    false,
                    30,
                    true
                );
            });
        }

        $html .= self::userInGroupsTable($user_id);

        $html .= '</div></div>';
        $this->renderInLayout($html);
    }

    static public function hasAccessToAdminParamsForm(){
        /** @var User $current_user_obj */
        $current_user_obj = Auth::currentUserObj();
        if (!$current_user_obj) {
            return false;
        }

        if (!$current_user_obj->getHasFullAccess()) {
            return false;
        }

        return true;
    }

    /**
     * Владельца и полный доступ пока показывает только пользователям с полным доступом.
     * @param $user_id
     * @return string
     */
    static public function adminParamsForm($user_id)
    {
        if (!self::hasAccessToAdminParamsForm()){
            return false;
        }

        $html = '';

        $html .= '<h2>Владельцы и полный доступ</h2>';

        $user_obj = User::factory($user_id);
        $html .= CForm::html(
            $user_obj,
            [
                new FRow(
                    'Owner user',
                    new FWInput(User::_OWNER_USER_ID, true)
                ),
                new FRow(
                    'Owner group',
                    new FWInput(User::_OWNER_GROUP_ID, true)
                ),
                new FRow(
                    'Primary group',
                    new FWInput(User::_PRIMARY_GROUP_ID, true)
                ),
                new FRow(
                    'Has full access',
                    new FWInput(User::_HAS_FULL_ACCESS)
                ),
            ]
        );

        return $html;
    }

    static public function commonParamsForm($user_id)
    {
        $html = '';

        //$html .= '<h4>Параметры</h4>';

        $user_obj = User::factory($user_id);
        $html .= CForm::html(
            $user_obj,
            [
                new FGroup(
                    'Login',
                    new FWInput('login')
                ),
                new FGroup(
                    'Комментарий',
                    new FWTextarea('description')
                )
            ]
        );

        return $html;
    }

    static public function passwordForm()
    {
        $html = '';

        $html .= '<div class="font-weight-bold mt-4">Изменение пароля</div>';
        $html .= '<form class="form-horizontal" role="form" method="post" action="' . Url::path() . '">';
        $html .= Form::op(self::OPERATION_SET_PASSWORD);

        $html .= '<div class="form-group">
<label class="">Новый пароль</label>
<div class="">
<input name="' . self::FIELD_NAME_PASSWORD . '" class="form-control" value="">
</div>
</div>';

        $html .= '<div class="text-right">
<button type="submit" class="btn btn-secondary">Сохранить</button>
</div>';

        $html .= '</form>';

        return $html;
    }

    static public function userInGroupsTable($user_id)
    {
        if (!Auth::currentUserHasAnyOfPermissions([AuthPermissions::PERMISSION_PHPAUTH_MANAGE_GROUPS])) {
            return '';
        }
        $html = '';

        //$html .= '<h4 class="mt-4 text-muted">Входит в группы</h4>';

        $new_user_to_group_obj = new UserToGroup();
        $new_user_to_group_obj->setUserId($user_id);

        $html .= CTable::html(
            UserToGroup::class,
            CForm::html(
                $new_user_to_group_obj,
                [
                    new FGroupHidden(new FWInput(UserToGroup::_USER_ID)),
                    new FGroup(
                        'Группа',
                        new FWReferenceAjax(UserToGroup::_GROUP_ID, Group::class, Group::_TITLE, (new GroupsListAjaxAction())->url(), (new GroupEditAction('REFERENCED_ID'))->url(), true)
                    )
                ]
            ),
            [
                new TCol(
                    '',
                    new TWTextWithLink(
                        //'{' . Group::class . '.{this->' . UserToGroup::_GROUP_ID . '}->' . Group::_TITLE . '}'
                        function(UserToGroup $utg){
                            return $utg->group() ? $utg->group()->title : 'NO GROUP';
                        },
                        //(new GroupEditAction('{this->' . UserToGroup::_GROUP_ID . '}'))->url()
                        function (UserToGroup $utg){
                            return (new GroupEditAction($utg->group_id))->url();
                        }
                    )
                ),
                new TCol(
                    '',
                    new TWDelete()
                )
            ],
            [
                new TFEqualHidden(UserToGroup::_USER_ID, $user_id)
            ],
            '',
            'gsdfhglyeryt',
            'Входит в группы'
        );

        return $html;
    }
}
