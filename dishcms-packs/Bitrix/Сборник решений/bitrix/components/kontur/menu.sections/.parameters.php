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
		'SEF_BASE_URL'=>array(
		    'PARENT'=>'SEF_MODE',
	    	"NAME"=>"Каталог ЧПУ (относительно корня сайта)",
		   	"TYPE"=>"STRING",
		   	"DEFAULT"=>"/catalog/"
		),
		'SECTION_PAGE_URL'=>array(
		    'PARENT'=>'SEF_MODE',
	    	"NAME"=>"Раздел",
		   	"TYPE"=>"STRING",
		   	"DEFAULT"=>"#SECTION_ID#/"
		),
		'DETAIL_PAGE_URL'=>array(
		    'PARENT'=>'SEF_MODE',
	    	"NAME"=>"Детальная информация",
		   	"TYPE"=>"STRING",
		   	"DEFAULT"=>"#SECTION_ID#/#ELEMENT_ID#/"
		),
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