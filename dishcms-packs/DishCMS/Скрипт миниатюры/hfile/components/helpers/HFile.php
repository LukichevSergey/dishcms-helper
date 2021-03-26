<?php
/**
 * File helper
 * 
 * @version 1.0
 */
class HFile
{
	/**
	 * Get file extension
	 * @param string $filename file name.
	 * @return string
	 */
	public static function getExt($filename)
	{
		return pathinfo($filename, PATHINFO_EXTENSION);
	}
	
	/**
	 * Получает только имя файла
	 * @param string $filename имя файла
	 */
	public static function getFileName($filename)
	{
		return pathinfo($filename, PATHINFO_FILENAME);
	}
	
	/**
	 * Удаляет расширение файла
	 * @param string $filename имя файла
	 * @param boolean $extDotCount кол-во точек в расширении. По умолчанию 0 (нуль).
	 */
	public static function removeExt($filename, $extDotCount=0)
	{
		return preg_replace('/^(.*?)(\.[^\.]+){0,' . ((int)$extDotCount + 1) . '}$/', '\\1', $filename);
	}
	
	/**
	 * File exists
	 * @param string $filename file name.
	 * @param boolean $notEmpty может ли файл быть пустым? По умолчанию FALSE - может.  
	 * @return boolean
	 */
	public static function fileExists($filename, $notEmpty=false) 
	{
		return is_file($filename) && (!$notEmpty || (filesize($filename) > 0));
	}
	
	/**
	 * Проверка является ли файл изображением.
	 * @param string $filename имя файла.
	 * @return boolean
	 */
	public static function fileExistsByImage($filename)
	{
		return self::fileExists($filename, true) && exif_imagetype($filename);
	}
	
	/**
	 * Get directory path
	 * @param string $path Path with filename
	 * @param boolean $close завершить путь DIRECTORY_SEPARATOR или нет.
	 * @return string
	 */
	public static function getDir($path, $close=false)
	{
		return pathinfo($path, PATHINFO_DIRNAME) . ($close ? DIRECTORY_SEPARATOR : '');
	}
	
	/**
	 * Make dir
	 * @see mkdir()
	 * Отличие в том, что происходит проверка того, создана ли директория или нет,
	 * и не принимает 4-го параметра $context.
	 */
	public static function mkDir($pathname, $mode=0755, $recursive=false)
	{ 
		return is_dir($pathname) ? true : mkdir($pathname, $mode, $recursive);
	}
	
	/**
	 * Преобразовать путь в URL
	 * на данный момент просто заменят символы "/" или "\" в "/".
	 * @param string $path путь
	 * @return string 
	 */
	public static function pathToUrl($path)
	{
		return preg_replace('/[\/\\\\]+/', '/', $path); 
	}
	
	/**
	 * Получить путь
	 * @param array $routes массив путей для склейки.
     * @param boolean $mkdir создавать директорию, если не существует. 
     *  По умолчанию (FALSE) - не создавать.
	 */
	public static function path($routes, $mkdir=false, $dirmode=0755)
	{
		$path=implode(DIRECTORY_SEPARATOR, $routes);
        
        if($mkdir) {
            if(!is_dir(dirname($path))) {
                self::mkDir(dirname($path), $dirmode, true);
            }
        }
        
        return $path;
	}
    
	public static function getBaseUrl($src)
    {
        return preg_replace('/^(.*)[\\\\\/]([^\\\\\/]+)$/', '$1', $src);
    }
    
    public static function thumb($src, $width, $height, $cacheTime=0, $forcy=false, $isFile=false, $adaptive=false)
    {
		if($isFile) $file=$src;
        else $file=$_SERVER['DOCUMENT_ROOT'] . $src;

        if(is_file($file)) {
            $tmb="{$width}_{$height}_".basename($file);
            $tmbFile=self::path([dirname($file), $tmb]);
            if(!$forcy && is_file($tmbFile)) {
                $forcy=($cacheTime > 0) ? ((time() - filectime($tmbFile)) > $cacheTime) : true;
            }
            if(!is_file($tmbFile) || $forcy || YII_DEBUG) {
            	$image=\Yii::app()->ih->load($file);
            	if($adaptive) {
            		$image=$image->adaptiveThumb($width, $height);
            	}
            	else {
            		$image=$image->resize($width, $height, true);
            	}
                $image->save($tmbFile);
            }
            
            return self::getBaseUrl($src) . '/'. $tmb;
        }
        
        return null;
    }
} 