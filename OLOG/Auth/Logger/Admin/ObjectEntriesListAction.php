<?php

namespace OLOG\Auth\Logger\Admin;

use OLOG\Auth\Operator;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\Exits;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;

class ObjectEntriesListAction extends LoggerAdminActionsBaseProxy implements
    InterfacePageTitle,
    InterfaceAction
{
    protected $object_fullid;

    public function __construct($object_fullid)
    {
        $this->object_fullid = $object_fullid;
    }

    public function url()
    {
        return '/admin/logger/objectentries/' . urlencode($this->object_fullid);
    }

    static public function urlMask()
    {
        return '/admin/logger/objectentries/([\w\.%]+)';
    }

    public function pageTitle()
    {
        return 'Объект ' . $this->object_fullid;
    }

    public function topActionObj()
    {
        return new EntriesListAction();
    }

    public function action()
    {
        Exits::exit403If(
            !Operator::currentOperatorHasAnyOfPermissions([\OLOG\Auth\Logger\Permissions::PERMISSION_PHPLOGGER_ACCESS])
        );

        $object_fullid = $this->object_fullid;

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
                new CRUDTableFilterEqualInvisible('object_fullid', $object_fullid)
            ],
            'created_at_ts desc'
        );

        AdminLayoutSelector::render($html, $this);
    }
}