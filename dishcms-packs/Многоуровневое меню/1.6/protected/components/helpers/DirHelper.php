<?php
/**
 * Dir Helper
 * 
 */
class DirHelper extends CComponent
{
	/**
	 * Get directory path
	 * @param string $path Path with filename
	 * @return string  
	 */
	public static function getDir($path)
	{
		return substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR));
	}
	
	/**
	 * Get only files in directory
	 * @param string $path path to directory.
	 * @return array
	 */
	public static function getFiles($path)
	{
		$d = dir($path);
		$files = array();
		while (false !== ($entry = $d->read())) {
			if(is_file($path . DIRECTORY_SEPARATOR . $entry))
				$files[] = $entry;
		}
		$d->close();
		
		return $files;
	}
}