<?php

namespace OLOG\Auth\Admin;

use OLOG\Auth\Operator;
use OLOG\Auth\OperatorPermission;
use OLOG\Auth\Permission;
use OLOG\Auth\Permissions;
use OLOG\Auth\User;
use OLOG\BT\BT;
use OLOG\BT\InterfaceBreadcrumbs;
use OLOG\BT\InterfacePageTitle;
use OLOG\BT\InterfaceUserName;
use OLOG\BT\Layout;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetReference;
use OLOG\CRUD\CRUDFormWidgetReferenceAjax;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilter;
use OLOG\CRUD\CRUDTableWidgetText;
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

        $html = CRUDForm::html(
            $operator_obj,
            [
                new CRUDFormRow(
                    'title',
                    new CRUDFormWidgetInput('title')
                ),
                new CRUDFormRow(
                    'User id',
                    new CRUDFormWidgetReferenceAjax('user_id', User::class, 'login', UsersListAjaxAction::getUrl(), UserEditAction::getUrl('REFERENCED_ID'))
                )
            ]
        );

        $new_operator_permission_obj = new OperatorPermission();
        $new_operator_permission_obj->setOperatorId($operator_id);

        $html .= '<h2>Назначенные разрешения</h2>';

        $html .= CRUDTable::html(
            OperatorPermission::class,
            CRUDForm::html(
                $new_operator_permission_obj,
                [
                    new CRUDFormRow(
                        'operator',
                        new CRUDFormWidgetReference('operator_id', Operator::class, 'title')
                    ),
                    new CRUDFormRow(
                        'permission',
                        new CRUDFormWidgetReference('permission_id', Permission::class, 'title')
                    )
                ]
            ),
            [
                new CRUDTableColumn('permission', new CRUDTableWidgetText('{\OLOG\Auth\Permission.{this->permission_id}->title}'))
            ],
            [
                new CRUDTableFilter('operator_id', CRUDTableFilter::FILTER_EQUAL, $operator_id)
            ]
        );

        Layout::render($html, $this);
    }
}