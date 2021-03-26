<?php
namespace Kontur\Ident\Component;

use Bitrix\Main;
use Bitrix\Main\Loader;
use Kontur\Ident\Helper;
use Kontur\Ident\TicketTable;

/**
 * Class Admin
 *
 * @package Kontur\Ident
 */
class Admin extends \CBitrixComponent
{
    /**
     * @inheritDoc
     *
     * @throws Main\SystemException
     */
    public function onPrepareComponentParams($arParams)
    {
        Loader::includeModule("iblock");
        Loader::includeModule("catalog");
        Loader::includeModule("kontur.ident");

        Helper::checkAccess();

        $arParams['CACHE_TYPE'] = 'N';

        return $arParams;
    }

    /** 
     * @inheritDoc
     */
    public function executeComponent()
    {        
        $this->includeComponentTemplate();
    }

    /**
     * Получить новые заявки, которые еще не были 
     * отправлены в IDENT.
     *
     * @return Main\DB\Result
     */
    public function getNewTicketsDbResult()
    {
        return TicketTable::getList([
            'filter'=>['IDENT_EXCHANGE_STATUS'=>Helper::STATUS_TICKET_NEW],
            'order'=>['DATE_AND_TIME'=>'DESC']
        ]);
    }

    /**
     * Получить заявки, которые уже были 
     * отправлены в IDENT.
     *
     * @return Main\DB\Result
     */
    public function getDoneTicketsDbResult()
    {
        return TicketTable::getList([
            'filter'=>['IDENT_EXCHANGE_STATUS'=>Helper::STATUS_TICKET_DONE],
            'order'=>['DATE_AND_TIME'=>'DESC']
        ]);
    }
}