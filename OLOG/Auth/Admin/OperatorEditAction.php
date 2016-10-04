<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\OperatorPermission;
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
use OLOG\CRUD\CRUDFormWidgetReferenceAjax;
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilter;
use OLOG\CRUD\CRUDTableFilterNotInInvisible;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\Exits;
use OLOG\Operations;
use OLOG\POSTAccess;
use OLOG\Url;

class OperatorEditAction
    implements InterfaceBreadcrumbs,
    InterfacePageTitle,
    InterfaceUserName
{
    use CurrentUserNameTrait;

    protected $operator_id;

    public function currentBreadcrumbsArr(){
        return self::breadcrumbsArr($this->operator_id);
    }

    static public function breadcrumbsArr($user_id)
    {
        return array_merge(OperatorsListAction::breadcrumbsArr(), [BT::a(self::getUrl($user_id), self::pageTitle($user_id))]);
    }

    public function currentPageTitle()
    {
        return self::pageTitle($this->operator_id);
    }

    static public function pageTitle($user_id){
        return 'Оператор ' . $user_id;
    }

    static public function getUrl($user_id = '(\d+)'){
        return '/admin/auth/operator/' . $user_id;
    }

    public function action($operator_id){
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_OPERATORS])
        );

        $this->operator_id = $operator_id;

        $operator_obj = Operator::factory($operator_id);

        $html = '<div class="col-md-6">';
        $html .= '<h2>Параметры</h2>';

        $html .= CRUDForm::html(
            $operator_obj,
            [
                new CRUDFormRow(
                    'title',
                    new CRUDFormWidgetInput('title')
                ),
                new CRUDFormRow(
                    'описание',
                    new CRUDFormWidgetTextarea('description')
                ),
                new CRUDFormRow(
                    'User id',
                    new CRUDFormWidgetReferenceAjax('user_id', User::class, 'login', UsersListAjaxAction::getUrl(), UserEditAction::getUrl('REFERENCED_ID'))
                )
            ]
        );

        $new_operator_permission_obj = new OperatorPermission();
        $new_operator_permission_obj->setOperatorId($operator_id);

        $html.= '</div><div class="col-md-6">';

        if (Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_OPERATORS])) {
            $html .= '<h2>Назначенные разрешения</h2>';

            $html .= HTML::div('', '', function () use ($operator_id) {
                $new_permissiontouser_obj = new OperatorPermission();
                $new_permissiontouser_obj->setOperatorId($operator_id);

                echo CRUDTable::html(
                    OperatorPermission::class,
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
                        new CRUDTableFilter('operator_id', CRUDTableFilter::FILTER_EQUAL, $operator_id)
                    ],
                    ''
                );

                echo CallapsibleWidget::buttonAndCollapse('Показать все неназначенные разрешения', function () use ($operator_id) {
                    $html = CRUDTable::html(
                        Permission::class,
                        '',
                        [
                            new CRUDTableColumn(
                                'Разрешение',
                                new CRUDTableWidgetTextWithLink('{this->title}', (new PermissionAddToOperatorAction($operator_id, '{this->id}'))->url())
                            ),
                            new CRUDTableColumn(
                                '',
                                new CRUDTableWidgetTextWithLink('Добавить оператору', (new PermissionAddToOperatorAction($operator_id, '{this->id}'))->url(), 'btn btn-default btn-xs'))
                        ],
                        [
                            new CRUDTableFilterNotInInvisible('id', OperatorPermission::getPermissionIdsArrForOperatorId($operator_id)),
                        ],
                        'id',
                        '79687tg8976rt87'
                    );
                    return $html;
                });
            });
        }
        $html.= '</div>';

        Layout::render($html, $this);
    }
}