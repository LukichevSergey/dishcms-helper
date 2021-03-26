<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $APPLICATION, $USER;

if(!empty($USER) && $USER->IsAdmin() && !$APPLICATION->GetShowIncludeAreas()) {
	$arResult['MAP_FILENAME']=dirname(__FILE__) . '/images/map.id.svg';
}
else {
	$arResult['MAP_FILENAME']=dirname(__FILE__) . '/images/map.svg';
}