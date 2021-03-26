<?php
/**
 * Hash helper
 * 
 * @version 1.0
 * 
 */
class HashHelper extends CComponent
{
	/**
	 * Генерирует хэш-строку
	 * @param string $str строка. Если передана пустое значение, строка генерится случайным образом.
	 * @param number $length длина возвращаемой строки.
	 * @return string сгенерированный хэш.
	 */
	public static function generateHash($str='', $length=0)
	{
		if(!$str) {
			list($usec, $sec) = explode(' ', microtime());
			$seed = (float) $sec + ((float) $usec * 100000);
			mt_srand($seed);
			$str = mt_rand();
		}
		
		return $length ? substr(sha1($str), 0, $length) : sha1($str);
	} 
}