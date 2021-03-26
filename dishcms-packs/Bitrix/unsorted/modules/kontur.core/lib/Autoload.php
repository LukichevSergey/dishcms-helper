<?php
/**
 * Автозагрузка классов
 *
 */
namespace Bitrix\Kontur\Core;

class Autoload 
{
	/**
	 * Подключение классов модуля.
	 * @param integer $moduleId id модуля.
	 * @param array $arClassesList массив классов вида array(sClassName=sClassFile)
	 */
	public static function moduleAutoloadClasses($moduleId, $arClassesList)
	{
		if (method_exists(\CModule, "AddAutoloadClasses"))
		{
			\CModule::AddAutoloadClasses( $moduleId, $arClassesList );
		}
		else
		{
			foreach ($arClassesList as $sClassName => $sClassFile)
			{
				require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$moduleId}/{$sClassFile}");
			}
		}
	}
}