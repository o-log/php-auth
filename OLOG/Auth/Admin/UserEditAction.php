<?php

namespace OLOG\Auth\Admin;

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
use OLOG\FullObjectId;
use OLOG\HTML;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;
use OLOG\Layouts\InterfaceTopActionObj;
use OLOG\Logger\Admin\ObjectEntriesListAction;
use OLOG\Operations;
use OLOG\POSTAccess;
use OLOG\Url;

class UserEditAction extends AuthAdminActionsBaseProxy implements
    InterfaceAction,
    InterfacePageTitle,
    InterfaceTopActionObj
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

    static public function urlMask()
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

        Operations::matchOperation(self::OPERATION_SET_PASSWORD, function () use ($user_id) {
            $new_password = POSTAccess::getOptionalPostValue(self::FIELD_NAME_PASSWORD);
            $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

            $user_obj = User::factory($user_id);
            $user_obj->setPasswordHash($new_password_hash);
            $user_obj->save();
        });

        $html = '';
        $html .= $this->getTabs($user_id);

        $html .= '<div class="row"><div class="col-md-6">';

        $html .= self::commonParamsForm($user_id);
        $html .= self::passwordForm();
        //$html .= self::userOperatorsTable($user_id);
        $html .= self::adminParamsForm($user_id);

        $html .= '</div><div class="col-md-6">';

        if (Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS])) {
            $html .= '<h2>Разрешения</h2>';
            $html .= HTML::div('', '', function () use ($user_id) {
                $new_permissiontouser_obj = new PermissionToUser();
                $new_permissiontouser_obj->setUserId($user_id);

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

                echo CollapsibleWidget::buttonAndCollapse('Показать все неназначенные разрешения', function () use ($user_id) {
                    echo CRUDTable::html(
                        Permission::class,
                        '',
                        [
                            new CRUDTableColumn(
                                'Разрешение',
                                new CRUDTableWidgetTextWithLink('{this->title}', (new PermissionAddToUserAction($user_id, '{this->id}'))->url())
                            ),
                            new CRUDTableColumn(
                                '',
                                new CRUDTableWidgetTextWithLink('+', (new PermissionAddToUserAction($user_id, '{this->id}'))->url(), 'btn btn-default btn-xs'))
                        ],
                        [
                            new CRUDTableFilterNotInInvisible('id', PermissionToUser::getPermissionIdsArrForUserId($user_id)),
                        ],
                        'title asc',
                        '79687tg8976rt87'
                    );
                });

            });
        }

        $html .= self::userInGroupsTable($user_id);

        $html .= '</div></div>';
        AdminLayoutSelector::render($html, $this);
    }

    /**
     * Владельца и полный доступ пока показывает только пользователям с полным доступом.
     * @param $user_id
     * @return string
     */
    static public function adminParamsForm($user_id)
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

        $html .= '<h2>Параметры</h2>';

        $user_obj = User::factory($user_id);
        $html .= CRUDForm::html(
            $user_obj,
            [
                new CRUDFormRow(
                    'Login',
                    new CRUDFormWidgetInput('login')
                ),
                new CRUDFormRow(
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

        $html .= '<h2>Изменение пароля</h2>';
        $html .= '<form class="form-horizontal" role="form" method="post" action="' . Url::getCurrentUrl() . '">';
        $html .= Operations::operationCodeHiddenField(self::OPERATION_SET_PASSWORD);

        $html .= '<div class="form-group ">
<label class="col-sm-4 text-right control-label">Новый пароль</label>
<div class="col-sm-8">
<input name="' . self::FIELD_NAME_PASSWORD . '" class="form-control" value="">
</div>
</div>';

        $html .= '<div class="row">
<div class="col-sm-8 col-sm-offset-4">
<button style="width: 100%" type="submit" class="btn btn-primary">Сохранить</button>
</div>
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

        $html .= '<h2>В составе групп</h2>';

        $new_user_to_group_obj = new UserToGroup();
        $new_user_to_group_obj->setUserId($user_id);

        $html .= CRUDTable::html(
            UserToGroup::class,
            CRUDForm::html(
                $new_user_to_group_obj,
                [
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(UserToGroup::_USER_ID)),
                    new CRUDFormRow('Группа',
                        new CRUDFormWidgetReferenceAjax(UserToGroup::_GROUP_ID, Group::class, Group::_TITLE, (new GroupsListAjaxAction())->url(), (new GroupEditAction('REFERENCED_ID'))->url(), true))
                ]
            ),
            [
                new CRUDTableColumn(
                    'Группа', new CRUDTableWidgetTextWithLink('{' . Group::class . '.{this->' . UserToGroup::_GROUP_ID . '}->' . Group::_TITLE . '}', (new GroupEditAction('{this->' . UserToGroup::_GROUP_ID . '}'))->url())
                ),
                new CRUDTableColumn(
                    'Удалить', new CRUDTableWidgetDelete()
                )
            ],
            [
                new CRUDTableFilterEqualInvisible(UserToGroup::_USER_ID, $user_id)
            ]
        );

        return $html;
    }

    public function getTabs($user_id)
    {
        $return = [
            \OLOG\BT\BT::tabHtml('Параметры', self::urlMask(), $this->url()),
            \OLOG\BT\BT::tabHtml('Журнал', '', (new ObjectEntriesListAction(FullObjectId::getFullObjectId(User::factory($user_id))))->url(), '_blank')
        ];
        return \OLOG\BT\BT::tabsHtml($return);
    }
}