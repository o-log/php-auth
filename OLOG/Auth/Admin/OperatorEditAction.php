<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\OperatorPermission;
use OLOG\Auth\Permission;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\BT\CollapsibleWidget;
use OLOG\HTML;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetReferenceAjax;
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableFilterNotInInvisible;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\Exits;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;
use OLOG\Layouts\InterfaceTopActionObj;

class OperatorEditAction extends AuthAdminActionsBaseProxy implements
    InterfaceAction,
    InterfacePageTitle,
    InterfaceTopActionObj
{
    protected $operator_id;

    public function topActionObj()
    {
        return new OperatorsListAction();
    }

    public function __construct($operator_id)
    {
        $this->operator_id = $operator_id;
    }

    public function pageTitle(){
        return 'Оператор ' . $this->operator_id;
    }

    public function url(){
        return '/admin/auth/operator/' . $this->operator_id;
    }

    static public function urlMask(){
        return '/admin/auth/operator/(\d+)';
    }

    public function action(){
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PHPAUTH_MANAGE_OPERATORS])
        );


        $operator_id = $this->operator_id;

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
                    new CRUDFormWidgetReferenceAjax('user_id', User::class, 'login', (new UsersListAjaxAction())->url(), (new UserEditAction('REFERENCED_ID'))->url())
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
                        new CRUDTableFilterEqualInvisible('operator_id', $operator_id)
                    ],
                    ''
                );

                echo CollapsibleWidget::buttonAndCollapse('Показать все неназначенные разрешения', function () use ($operator_id) {
                    echo CRUDTable::html(
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
                });
            });
        }
        $html.= '</div>';

        AdminLayoutSelector::render($html, $this);
    }
}