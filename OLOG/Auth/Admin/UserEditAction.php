<?php

namespace OLOG\Auth\Admin;

use OLOG\ActionInterface;
use OLOG\Auth\Auth;
use OLOG\Auth\Group;
use OLOG\Auth\Operator;
use OLOG\Auth\OwnerCheck;
use OLOG\Auth\Permission;
use OLOG\Auth\Permissions;
use OLOG\Auth\PermissionToUser;
use OLOG\Auth\User;
use OLOG\Auth\UserToGroup;
use OLOG\BT\CollapsibleWidget;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormInvisibleRow;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormVerticalRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetReference;
use OLOG\CRUD\CRUDFormWidgetReferenceAjax;
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableFilterNotInInvisible;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\Exits;
use OLOG\Form;
use OLOG\HTML;
use OLOG\Layouts\AdminLayoutSelector;
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
        return 'Пользователь ' . $this->user_id;
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
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS])
        );

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

        if (Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS])) {
            $html .= '<h4 class="text-muted">Разрешения <span>' . MagnificPopup::button('jjhgdkshgdsfg3456', 'btn btn-secondary btn-sm', '<i class="fa fa-plus"></i>') . '</span></h4>';
            $html .= HTML::div('', '', function () use ($user_id) {
                $new_permissiontouser_obj = new PermissionToUser();
                $new_permissiontouser_obj->setUserId($user_id);

                //echo CollapsibleWidget::buttonAndCollapse('Неназначенные разрешения', function () use ($user_id) {
                $popup_fn = function () use ($user_id) {
                    $html = '';
                    $html = '<h4 class="text-muted">Неназначенные пользователю разрешения</h4>';
                    $html .= CRUDTable::html(
                        Permission::class,
                        '',
                        [
                            new CRUDTableColumn(
                                '',
                                new CRUDTableWidgetTextWithLink('{this->title}', (new PermissionAddToUserAction($user_id, '{this->id}'))->url())
                            ),
                            new CRUDTableColumn(
                                '',
                                new CRUDTableWidgetTextWithLink('+', (new PermissionAddToUserAction($user_id, '{this->id}'))->url(), 'btn btn-secondary btn-sm'))
                        ],
                        [
                            new CRUDTableFilterNotInInvisible('id', PermissionToUser::getPermissionIdsArrForUserId($user_id)),
                        ],
                        'title asc',
                        '79687tg8976rt87'
                    );

                    return $html;
                };

                echo MagnificPopup::popupHtml('jjhgdkshgdsfg3456', $popup_fn());

                echo CRUDTable::html(
                    PermissionToUser::class,
                    '',
                    [
                        new \OLOG\CRUD\CRUDTableColumn(
                            '', new \OLOG\CRUD\CRUDTableWidgetText('{' . Permission::class . '.{this->permission_id}->title}')
                        ),
                        new \OLOG\CRUD\CRUDTableColumn(
                            '', new \OLOG\CRUD\CRUDTableWidgetDelete()
                        )
                    ],
                    [
                        new CRUDTableFilterEqualInvisible('user_id', $user_id)
                    ],
                    ''
                );


            });
        }

        $html .= self::userInGroupsTable($user_id);

        $html .= '</div></div>';
        AdminLayoutSelector::render($html, $this);
    }

    static public function hasAccessToAdminParamsForm(){
        if (Auth::fullAccessCookiePresentInRequest()){
            return true;
        }

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
        $html .= CRUDForm::html(
            $user_obj,
            [
                new CRUDFormRow(
                    'Owner user',
                    new CRUDFormWidgetInput(User::_OWNER_USER_ID, true)
                ),
                new CRUDFormRow(
                    'Owner group',
                    new CRUDFormWidgetInput(User::_OWNER_GROUP_ID, true)
                ),
                new CRUDFormRow(
                    'Primary group',
                    new CRUDFormWidgetInput(User::_PRIMARY_GROUP_ID, true)
                ),
                new CRUDFormRow(
                    'Has full access',
                    new CRUDFormWidgetInput(User::_HAS_FULL_ACCESS)
                ),
            ]
        );

        return $html;
    }

    static public function commonParamsForm($user_id)
    {
        $html = '';

        $html .= '<h4 class="text-muted">Параметры</h4>';

        $user_obj = User::factory($user_id);
        $html .= CRUDForm::html(
            $user_obj,
            [
                new CRUDFormVerticalRow(
                    'Login',
                    new CRUDFormWidgetInput('login')
                ),
                new CRUDFormVerticalRow(
                    'Комментарий',
                    new CRUDFormWidgetTextarea('description')
                )
            ]
        );

        return $html;
    }

    static public function passwordForm()
    {
        $html = '';

        $html .= '<h4 class="mt-4 text-muted">Изменение пароля</h4>';
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

    /*
    static public function userOperatorsTable($user_id)
    {
        $html = '';

        $html .= '<h2>Операторы пользователя</h2>';

        $new_operator_obj = new Operator();
        $new_operator_obj->setUserId($user_id);

        $html .= \OLOG\CRUD\CRUDTable::html(
            \OLOG\Auth\Operator::class,
            CRUDForm::html(
                $new_operator_obj,
                [
                    new CRUDFormRow(
                        'user_id',
                        new CRUDFormWidgetReference('user_id', User::class, 'login')
                    ),
                    new CRUDFormRow(
                        'title',
                        new CRUDFormWidgetInput('title')
                    ),
                    new CRUDFormRow(
                        'Описание',
                        new CRUDFormWidgetTextarea('description')
                    )
                ]
            ),
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    'title', new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->title}', (new OperatorEditAction('{this->id}'))->url())
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Описание', new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->description}', (new OperatorEditAction('{this->id}'))->url())
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Удалить', new \OLOG\CRUD\CRUDTableWidgetDelete()
                )
            ],
            [
                new CRUDTableFilterEqualInvisible('user_id', $user_id)
            ]
        );

        return $html;
    }
    */

    static public function userInGroupsTable($user_id)
    {
        if (!Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_GROUPS])) {
            return '';
        }
        $html = '';

        $html .= '<h4 class="mt-4 text-muted">Входит в группы</h4>';

        $new_user_to_group_obj = new UserToGroup();
        $new_user_to_group_obj->setUserId($user_id);

        $html .= CRUDTable::html(
            UserToGroup::class,
            CRUDForm::html(
                $new_user_to_group_obj,
                [
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(UserToGroup::_USER_ID)),
                    new CRUDFormVerticalRow(
                        'Группа',
                        new CRUDFormWidgetReferenceAjax(UserToGroup::_GROUP_ID, Group::class, Group::_TITLE, (new GroupsListAjaxAction())->url(), (new GroupEditAction('REFERENCED_ID'))->url(), true)
                    )
                ]
            ),
            [
                new CRUDTableColumn(
                    '', new CRUDTableWidgetTextWithLink('{' . Group::class . '.{this->' . UserToGroup::_GROUP_ID . '}->' . Group::_TITLE . '}', (new GroupEditAction('{this->' . UserToGroup::_GROUP_ID . '}'))->url())
                ),
                new CRUDTableColumn(
                    '', new CRUDTableWidgetDelete()
                )
            ],
            [
                new CRUDTableFilterEqualInvisible(UserToGroup::_USER_ID, $user_id)
            ],
            '',
            'gsdfhglyeryt',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        return $html;
    }
}