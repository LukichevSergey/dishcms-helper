<?
/**
 * Tools. File.
 */
namespace Kontur\Core\Main\Tools;

class File
{
    const DS = '/'; //DIRECTORY_SEPARATOR;
    
    /**
     * @see \CFile::ResizeImageGet()
     *
     * @param integer $resizeType тип масштабирования: 
     *  BX_RESIZE_IMAGE_EXACT - масштабирует в прямоугольник $arSize c сохранением пропорций, обрезая лишнее; 
     *  BX_RESIZE_IMAGE_PROPORTIONAL - масштабирует с сохранением пропорций, размер ограничивается $arSize; 
     *  BX_RESIZE_IMAGE_PROPORTIONAL_ALT - масштабирует с сохранением пропорций за ширину при этом принимается 
     *  максимальное значение из высоты/ширины, размер ограничивается $arSize, улучшенная обработка 
     *  вертикальных картинок. 
     */
	public static function getResizeImage(
        $file, 
        $width, 
        $height, 
        $resizeType=BX_RESIZE_IMAGE_PROPORTIONAL,
        $filters=false,
        $immediate=false,
        $jpgQuality=false
    )
	{
        return \CFile::ResizeImageGet($file, ['width'=>$width, 'height'=>$height], $resizeType, false, $filters, $immediate, $jpgQuality);
	}
    
    public static function getResizeImageSrc(
        $file, 
        $width, 
        $height, 
        $resizeType=BX_RESIZE_IMAGE_PROPORTIONAL,
        $filters=false,
        $immediate=false,
        $jpgQuality=false
    )
    {
        if($result=self::getResizeImage($file, $width, $height, $resizeType, $filters, $immediate, $jpgQuality)) {
            return $result['src'];
        }
        
        return false;
    }
    
    /**
     * @param string|FALSE $destDir путь к файлу миниатюре. 
     * По умолчанию NULL (DOCUMENT_ROOT . "/upload/resize_cache/" . crc32($sourceFile))
     * Может быть также передано:
     * FALSE: файл изображения будет создан в папке с оригинальным изображением.
     */
    public static function getResizeImageFile(
        $sourceFile, 
        $width, 
        $height, 
        $resizeType=BX_RESIZE_IMAGE_PROPORTIONAL, 
        $waterMark=[], 
        $destDir=null, 
        $forcy=true
    )
    {
        if(is_file($sourceFile)) {
            $io = \CBXVirtualIo::GetInstance();
            
            $sourceInfo=pathinfo($sourceFile);             
            
            if($destDir === null) {
                $hash=crc32($sourceFile);
                if($hash < 0) $hash *= -1;
                $destDir=$io->RelativeToAbsolutePath(self::path(['upload', 'resize_cache', 'kfile_resized', $hash]));
                $io->CreateDirectory($destDir);
            }
            elseif($destDir === false) {
                $destDir=$sourceInfo['dirname'];
            }
            
            $destFile=$destDir . self::DS . $sourceInfo['filename'] . '_' . $width . '_' . $height . '.' . $sourceInfo['extension'];
            
            if(!is_file($destFile) || $forcy) {
                \CFile::ResizeImageFile($sourceFile, $destFile, ['width'=>$width, 'height'=>$height], $resizeType, $waterMark);
            }
            
            return $destFile;
        }
        return false;
    }
    
    public static function getResizeImageBySrc($src, $width, $height, $resizeType=BX_RESIZE_IMAGE_PROPORTIONAL, $waterMark=[])
    {
        if($destFile=self::getResizeImageFile(self::getPathBySrc($src), $width, $height, $resizeType, $waterMark))
        {
            return self::getSrcByPath($destFile);
        }
        
        return false;
    }
    
    public static function getSrcByPath($path, $docRoot=false)
    {
        if(!$docRoot) {
            $docRoot=$_SERVER['DOCUMENT_ROOT'];
        }
        $docRoot=rtrim($docRoot, '\/');
        
        return preg_replace('#^('.str_replace('.', '\.', $docRoot) . ')[/\\\\](.*?)$#i', '/$2', $path);
    }
    
    public static function getPathBySrc($src, $docRoot=false)
    {
        if(!$docRoot) {
            $io = \CBXVirtualIo::GetInstance();
            return $io->RelativeToAbsolutePath($src);
        }
        
        return $docRoot . self::DS . $src;
    }
    
    public static function path($chunks)
    {
        return implode(self::DS, $chunks);
    }
}