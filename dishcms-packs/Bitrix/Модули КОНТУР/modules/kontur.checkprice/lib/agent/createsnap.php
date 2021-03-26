<?php
namespace Kontur\CheckPrice\Agent;

use Bitrix\Main\Loader;
use Kontur\CheckPrice\Helper;
use Kontur\CheckPrice\SnapTable;

class CreateSnap
{
    public static function run()
    {
        Loader::includeModule("iblock");
        Loader::includeModule("catalog");

        Helper::isAgent(true);

        // создаем новый снимок
        SnapTable::createSnap();

        return '\Kontur\CheckPrice\Agent\CreateSnap::run();';
    }
}