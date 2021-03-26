<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Context;

Loader::includeModule("kontur.ident");

\Kontur\Ident\Exchange::run(Context::getCurrent()->getServer()->get('REDIRECT_IDENT_ACTION'));