<?php
namespace Kontur\CheckPrice\Agent;

use Bitrix\Main\Loader;
use Kontur\CheckPrice\Helper;
use Kontur\CheckPrice\SnapTable;

class CreateSnapNext
{
    public static function run()
    {
        Loader::includeModule("iblock");
        Loader::includeModule("catalog");

        Helper::isAgent(true);

        // получаем последний снимок со статусом в Процессе
        if($snap=SnapTable::getLastRunnedSnap()) {
            SnapTable::createSnapNextBySnap($snap);

            if($snap=SnapTable::getSnapById($snap->getId())) {
                if($snap->getStatus() == Helper::SNAP_STATUS_DONE) {
                    Helper::sendPriceList($snap->getId());
                }
            }
        }

        return '\Kontur\CheckPrice\Agent\CreateSnapNext::run();';
    }
}