<?php
namespace Kontur\Ident\Agent;

use Bitrix\Main\Application;
use Kontur\Ident\Helper;
use Kontur\Ident\TicketTable;

/**
 * Агент удаления уже отправленных в IDENT заявок
 */
class DeleteOldTickets
{
    public static function run()
    {
        $sql='DELETE FROM `' . (new TicketTable)->getTableName() . '` WHERE `DATE_AND_TIME` < DATE_SUB(NOW(), INTERVAL 3 DAY)';
        
        $connection=Application::getConnection();
        $connection->query($sql);

        return '\Kontur\Ident\Agent\DeleteOldTickets::run();';
    }
}