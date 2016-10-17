<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Auth;
use OLOG\Auth\Operator;
use OLOG\Auth\OwnerCheck;
use OLOG\Auth\Permission;
use OLOG\Auth\Permissions;
use OLOG\Auth\PermissionToUser;
use OLOG\Auth\User;
use OLOG\BT\BT;
use OLOG\BT\CallapsibleWidget;
use OLOG\BT\HTML;
use OLOG\BT\InterfaceBreadcrumbs;
use OLOG\BT\InterfacePageTitle;
use OLOG\BT\InterfaceUserName;
use OLOG\BT\Layout;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetReference;
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableFilterNotInInvisible;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\Exits;
use OLOG\Operations;
use OLOG\POSTAccess;
use OLOG\Url;

class UserEditAction
    implements InterfaceBreadcrumbs,
    InterfacePageTitle,
    InterfaceUserName
{
    use CurrentUserNameTrait;

    const OPERATION_SET_PASSWORD = 'OPERATION_SET_PASSWORD';
    const FIELD_NAME_PASSWORD = 'password';

    protected $user_id;

    public function currentBreadcrumbsArr(){
        return self::breadcrumbsArr($this->user_id);
    }

    static public function breadcrumbsArr($user_id)
    {
        return array_merge(UsersListAction::breadcrumbsArr(), [BT::a(self::getUrl($user_id), self::pageTitle($user_id))]);
    }

    public function currentPageTitle()
    {
        return self::pageTitle($this->user_id);
    }

    static public function pageTitle($user_id){
        return 'Пользователь ' . $user_id;
    }

    static public function getUrl($user_id = '(\d+)'){
        return '/admin/auth/user/' . $user_id;
    }

    public function action($user_id){
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS])
        );

        $this->user_id = $user_id;
        $user_obj = User::factory($user_id);

        Exits::exit403If(
            !OwnerCheck::currentUserOwnsObj($user_obj)
        );

        Operations::matchOperation(self::OPERATION_SET_PASSWORD, function() use ($user_id) {
            $new_password = POSTAccess::getOptionalPostValue(self::FIELD_NAME_PASSWORD);
            $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

            $user_obj = User::factory($user_id);
            $user_obj->setPasswordHash($new_password_hash);
            $user_obj->save();
        });

        $html = '';

        $html .= '<div class="row"><div class="col-md-6">';

        $html .= self::commonParamsForm($user_id);
        $html .= self::passwordForm();
        $html .= self::userOperatorsTable($user_id);
        $html .= self::adminParamsForm($user_id);

        $html .= '</div><div class="col-md-6">';

        if (Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_USERS_PERMISSIONS])) {
            $html .= '<h2>Разрешения</h2>';
            $html .= HTML::div('', '',  function () use ($user_id) {
                $new_permissiontouser_obj = new PermissionToUser();
                $new_permissiontouser_obj->setUserId($user_id);

                echo CRUDTable::html(
                    PermissionToUser::class,
                    '',
                    [
                        new \OLOG\CRUD\CRUDTableColumn(
                            'Разрешение', new \OLOG\CRUD\CRUDTableWidgetText('{' . Permission::class . '.{this->permission_id}->title}')
                        ),
                        new \OLOG\CRUD\CRUDTableColumn(
                            'Удалить', new \OLOG\CRUD\CRUDTableWidgetDelete()
                        )
                    ],
                    [
                        new CRUDTableFilterEqualInvisible('user_id', $user_id)
                    ],
                    ''
                );

                echo CallapsibleWidget::buttonAndCollapse('Показать все неназначенные разрешения', function () use($user_id) {
                    $html = CRUDTable::html(
                        Permission::class,
                        '',
                        [
                            new CRUDTableColumn(
                                'Разрешение',
                                new CRUDTableWidgetTextWithLink('{this->title}', (new PermissionAddToUserAction($user_id, '{this->id}'))->url())
                            ),
                            new CRUDTableColumn(
                                '',
                                new CRUDTableWidgetTextWithLink('Добавить пользователю', (new PermissionAddToUserAction($user_id, '{this->id}'))->url(), 'btn btn-default btn-xs'))
                        ],
                        [
                            new CRUDTableFilterNotInInvisible('id', PermissionToUser::getPermissionIdsArrForUserId($user_id)),
                        ],
                        'id',
                        '79687tg8976rt87'
                    );
                    return $html;
                });

            });
        }

        $html .= '</div></div>';
        Layout::render($html, $this);
    }

    /**
     * Владельца и полный доступ пока показывает только пользователям с полным доступом.
     * @param $user_id
     * @return string
     */
    static public function adminParamsForm($user_id){
        /** @var User $current_user_obj */
        $current_user_obj = Auth::currentUserObj();
        if (!$current_user_obj){
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

    static public function commonParamsForm($user_id){
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

    static public function passwordForm(){
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

    static public function userOperatorsTable($user_id){
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
                    'title', new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->title}', OperatorEditAction::getUrl('{this->id}'))
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Описание', new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->description}', OperatorEditAction::getUrl('{this->id}'))
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
}