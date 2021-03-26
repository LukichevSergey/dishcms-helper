<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

require_once dirname(__FILE__) . '/kontur/handlers/UpdateCatalogQuantityHandler.php';
\kontur\handlers\UpdateCatalogQuantityHandler::init(<CATALOG_IBLOCK_ID>, <OFFER_IBLOCK_ID>);