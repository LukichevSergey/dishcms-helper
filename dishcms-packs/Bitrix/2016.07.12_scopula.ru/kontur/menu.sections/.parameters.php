<? 
/**
 * @link https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2132
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters=array(
	'GROUPS'=>array(
		'MENU_SETTINGS'=>array(
			'NAME'=>'Настройки меню'
		)
	),
	'PARAMETERS'=>array(
		'IBLOCK_TYPE'=>array(
		    'PARENT'=>'BASE',
	    	"NAME"=>"Тип инфоблока каталога",
		   	"TYPE"=>"STRING",
    		"REFRESH"=>"N"
		),
		'IBLOCK_ID'=>array(
	    	'PARENT'=>'BASE',
		    "NAME"=>"Идентификатор инфоблока каталога",
		   	"TYPE"=>"STRING",
	    	"REFRESH"=>"N"
		),
		'DEPTH_LEVEL'=>array(
		    'PARENT'=>'MENU_SETTINGS',
	    	"NAME"=>"Глубина вложенности разделов",
		   	"TYPE"=>"STRING",
    		"REFRESH"=>"N"
		)
	)
);

?>