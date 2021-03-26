<?
/**
 * Iblock\Tools\File
 */
namespace Kontur\Core\Iblock\Tools;

use Kontur\Core\Main;

class File
{
    /**
     * @const MODE_PREVIEW_DETAIL сперва проверяется, есть ли PREVIEW_PICTURE, если да
     *  то отображается она, если нет проверяется наличие DETAIL_PICTURE.
     */
    const MODE_PREVIEW_DETAIL=0;
    
    /**
     * @const MODE_PREVIEW отображается только PREVIEW_PICTURE
     */
    const MODE_PREVIEW=1;
    
    /**
     * @const MODE_DETAIL отображается только DETAIL_PICTURE
     */
    const MODE_DETAIL=2; 
    
    /**
     * @const MODE_DETAIL_PREVIEW сперва проверяется, есть ли DETAIL_PICTURE, если да
     *  то отображается она, если нет проверяется наличие PREVIEW_PICTURE.
     */
    const MODE_DETAIL_PREVIEW=3;
    
    /**
     * @see \CFile::ShowImage()
     *
     * @param array $arItem массив элемента инфоблока, который содержит 
     * PREVIEW_PICTURE или DETAIL_PICTURE.
     * 
     * @param integer $mode режим отображения. По умолчанию 0(self::MODE_PREVIEW_DETAIL).
     */
    public static function getImg(
        $arItem, 
        $imageUrl='',
        $mode=0, 
        $width=0, 
        $height=0, 
        $default='',
        $resizeType=BX_RESIZE_IMAGE_PROPORTIONAL,
        $params='', 
        $popup=false, 
        $popupTitle=false,
        $waterMark=[],
        $previewPictureName='PREVIEW_PICTURE', 
        $detailPictureName='DETAIL_PICTURE'
    )
    {
        if($src=self::getSrc($arItem, $mode, $width, $height, $resizeType, $default, $previewPictureName, $detailPictureName)) {
            return \CFile::ShowImage($src, $width, $height, $params, $imageUrl, $popup, $popupTitle, $width, $height);
        }
        
        return '';
    }
    
    public static function get2Img(
        $arItem, 
        $width=0, 
        $height=0, 
        $default='', 
        $resizeType=BX_RESIZE_IMAGE_PROPORTIONAL, 
        $params='', 
        $alt=false, 
        $previewPictureName='PREVIEW_PICTURE', 
        $detailPictureName='DETAIL_PICTURE'
    )
    {
        if($preview=self::getSrc($arItem, self::MODE_PREVIEW_DETAIL, $width, $height, $resizeType, $default, $previewPictureName, $detailPictureName)) {
            if($preview != $default) {
                $detail=self::getSrc($arItem, self::MODE_DETAIL_PREVIEW, 0, 0, BX_RESIZE_IMAGE_PROPORTIONAL, false, $previewPictureName, $detailPictureName);
                return \CFile::Show2Images($preview, $detail, $width, $height, $params, $alt, $width, $height);
            }
            else {
                return \CFile::ShowImage($preview, $width, $height, $params, '', false, false, $width, $height);
            }
        }
        
        return '';
    }
    
    public static function getSrc(
        $arItem, 
        $mode, 
        $width=0, 
        $height=0, 
        $resizeType=BX_RESIZE_IMAGE_PROPORTIONAL, 
        $default=false, 
        $previewPictureName='PREVIEW_PICTURE', 
        $detailPictureName='DETAIL_PICTURE'
    )
    {
        $fileData=false;
        
        switch($mode) {
            case self::MODE_PREVIEW:
                $fileData=Main\Tools\Data::get($arItem, $previewPictureName, false);
                break;
            case self::MODE_DETAIL:
                $fileData=Main\Tools\Data::get($arItem, $detailPictureName, false);
                break;
            case self::MODE_DETAIL_PREVIEW:
                if(!($fileData=Main\Tools\Data::get($arItem, $detailPictureName, false))) {
                    $fileData=Main\Tools\Data::get($arItem, $previewPictureName, false);
                }
                break;
            default:   
                if(!($fileData=Main\Tools\Data::get($arItem, $previewPictureName, false))) {
                    $fileData=Main\Tools\Data::get($arItem, $detailPictureName, false);
                }
        }
        
        if($fileData) {
            if(is_numeric($fileData)) {
                $src=$fileData;
            }
            elseif(is_array($fileData)) {
                $src=$fileData['SRC'];
            }
            else {
            	$src=$fileData;
            }
            
            if(is_numeric($src)) {
                if($width && $height) {
                    $src=Main\Tools\File::getResizeImageSrc($src, $width, $height, $resizeType);
                }
                elseif($fileData=\CFile::GetFileArray($src)) {
                    $src=$fileData['SRC'];
                }
                else {
                    $src=false;
                }
            }
            elseif($width && $height) {
                $src=Main\Tools\File::getResizeImageBySrc($src, $width, $height, $resizeType);
            }
        }
        
        return $src ?: $default;
    }
}