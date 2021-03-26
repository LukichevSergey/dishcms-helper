<?php
namespace ext\uploader\components\helpers;

class HUploader
{
	private static $path;
	
	public static function setBasePath($path)
	{
		static::$path=$path;
		
		return !empty($path);
	}
	
	public static function getBaseUrl()
	{
		if($path=static::getBasePath()) {
			$webroot=\Yii::getPathOfAlias('webroot');
			if(strpos($path, $webroot) === 0) {
				return '/' . trim(preg_replace('#[/\\\\]+#', '/', substr($path, strlen($webroot))), '/');
			}
		}
		return false;
	}
	
	public static function getBasePath()
	{
		if(!static::$path) {
			return false;
		}
		
		$path=\Yii::getPathOfAlias(static::$path);
		if(!is_dir($path)) {
			mkdir($path, 0755, true);
			chmod($path, 0755);
		}
		
		return is_dir($path) ? $path : false;
	}
	
	public static function getFiles($dirname, $fullpath=false, $hashes=[], $not=false)
    {
        $files = [];

        if(is_dir($dirname)) {
        	if(is_string($hashes)) {
        		$hashes=[$hashes];
        	}
        	
            $d = dir($dirname);
            while (false !== ($entry = $d->read())) {
                if(is_file($dirname. DIRECTORY_SEPARATOR . $entry)) {
                	$allow=true;
                	if(!empty($hashes)) {
                		$allow=false;
                		foreach($hashes as $hash) {
                			if(strpos($entry, $hash."_") === 0) {
                				$allow=true;
                				break;
                			}
                		}
                		
                		if($not) {
                			$allow=!$allow;
                		}
                	}
                	
                	if($allow) {
	                    if($fullpath) {
	                        $files[] = $dirname. DIRECTORY_SEPARATOR . $entry;
	                    }
	                    else {
	                        $files[] = $entry;
	                    }
                	}
                }
            }
            $d->close();
        }
    
        return $files;
    }
    
    /**
     * Форматированный вывод размера файла/директории
     * @link http://stackoverflow.com/a/16765613
     * @param integer $B размер
     * @param number $D размер дробной части. По умолчанию 2(два числа) 
     * @return string
     */
    public static function formatSize($B, $D=2){
        $S = 'kMGTPEZY';
        $F = floor((strlen($B) - 1) / 3);
        return preg_replace('/\.0+$/', '', sprintf("%.{$D}f", $B/pow(1024, $F))).' '.@$S[$F-1].'B';
    }
}