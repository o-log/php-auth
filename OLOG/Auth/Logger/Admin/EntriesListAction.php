<?php

namespace OLOG\Auth\Logger\Admin;

use OLOG\Auth\Operator;
use OLOG\Exits;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;

class EntriesListAction extends LoggerAdminActionsBaseProxy implements
    InterfacePageTitle,
    InterfaceAction
{

    public function url()
    {
        return '/admin/logger/entries';
    }

    public function pageTitle()
    {
        return 'Журнал';
    }

    public function action()
    {
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([\OLOG\Auth\Logger\Permissions::PERMISSION_PHPLOGGER_ACCESS])
        );

        $html = \OLOG\CRUD\CRUDTable::html(
            \OLOG\Auth\Logger\Entry::class,
            '',
            [
                new \OLOG\CRUD\CRUDTableColumn(
                    'Объект',
                    new \OLOG\CRUD\CRUDTableWidgetText('{this->object_fullid}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Дата создания',
                    new \OLOG\CRUD\CRUDTableWidgetTimestamp('{this->created_at_ts}')
                ),
                new \OLOG\CRUD\CRUDTableColumn(
                    'Пользователь',
                    new \OLOG\CRUD\CRUDTableWidgetTextWithLink('{this->user_fullid}', (new EntryEditAction('{this->id}'))->url())
                )
            ],
            [
                new \OLOG\CRUD\CRUDTableFilterLike('38947yt7ywssserkit22uy', 'Object Fullid', \OLOG\Auth\Logger\Entry::_OBJECT_FULLID),
            ],
            'created_at_ts desc',
            '8273649529',
            \OLOG\CRUD\CRUDTable::FILTERS_POSITION_TOP
        );

        AdminLayoutSelector::render($html, $this);
    }
}