<?php
namespace Kontur\CheckPrice\Agent;

use Bitrix\Main\Loader;
use Kontur\CheckPrice\Helper;
use Kontur\CheckPrice\SnapTable;

class SendSnapEmailTest
{
    public static function run($snapId=null)
    {
        Loader::includeModule("iblock");
        Loader::includeModule("catalog");

        Helper::isAgent(true);

        if(is_numeric($snapId)) {
            if($snap=SnapTable::getSnapById($snapId)) {
                Helper::sendPriceList($snap->getId());
            }
        }

        return '\Kontur\CheckPrice\Agent\SendSnapEmailTest::run();';
    }
}