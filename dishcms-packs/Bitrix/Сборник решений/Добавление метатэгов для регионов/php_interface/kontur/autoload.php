<?
/**
 * Подключение в init.php
 * include_once dirname(__FILE__) . '/kontur/autoload.php';
 */
include(dirname(__FILE__).'/functions/GetCities.php');
include(dirname(__FILE__).'/functions/GetCityPrefix.php');

defined('CITY_PREFIX') or define('CITY_PREFIX', \kontur\GetCityPrefix());
defined('CITY_PREFIX_UF') or define('CITY_PREFIX_UF', strtoupper(CITY_PREFIX).'_');

include(dirname(__FILE__) . '/functions/KonturGetMetaDescName.php');
include(dirname(__FILE__) . '/functions/KonturGetSectionUFProp.php');
