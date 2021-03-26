<?php
require_once dirname(__FILE__) . '/kontur/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/components/kontur/regions.citychange/lib/helper.php';
KRCCHelper::i()->init(['IBLOCK_ID'=>59, 'COOKIE_KEY'=>'krccity', 'PROPERTY_IS_DEFAULT_CODE'=>'IS_DEFAULT']);
