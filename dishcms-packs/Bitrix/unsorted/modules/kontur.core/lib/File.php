<?php
/**
 * Класс работы с файлами
 *
 */
namespace Bitrix\Kontur\Core;

class File 
{
	/**
	 * Получить путь. 
	 * @param array $arPathChunks массив кусков пути, которые будут склеены через DIRECTORY_SEPARATOR. 
	 * @param string $sFileName имя файла, относительно которого будут сформирован путь. 
	 * к данному параметру будет применено dirname($sFileName).
	 */
	public static function getPath($arPathChunks, $sFileName=null)
	{
		return (empty($sFileName) ? '' : dirname($sFileName)) . implode(DIRECTORY_SEPARATOR, $arPathChunks);
	}
}