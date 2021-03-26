<?php
namespace Kontur\CheckPrice\Agent;

use Bitrix\Main\Application;
use Kontur\CheckPrice\SnapTable;

/**
 * Агент удаления устаревших снимков цен
 */
class DeleteOldSnaps
{
    public static function run($safeCount=100)
    {
        $safeCount=(int)$safeCount < 10 ? 10 : (int)$safeCount;

        $connection=Application::getConnection();
        // используется обертка "... SELECT * FROM ..." для корректной работы запроса в MariaDb 
        // https://jira.mariadb.org/browse/MDEV-12137
        $sql='DELETE FROM `'
            . (new SnapTable)->getTableName() 
            . '` WHERE `ID` NOT IN (SELECT * FROM ('
                . 'SELECT `ID` FROM `' . (new SnapTable)->getTableName() . '` ORDER BY `CREATE_TIME` DESC LIMIT ' . $safeCount 
            . ') AS `t`)';

        $connection->query($sql);

        return '\Kontur\CheckPrice\Agent\DeleteOldSnaps::run('.$safeCount.');';
    }
}