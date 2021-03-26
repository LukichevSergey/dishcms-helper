<?
if(!function_exists("KonturGetImageSrc"))
{
    /**
     * Получение изображения.
     * Для получения миниатюры параметры $width и $height обязательны.
     *
     * @param int $FILE_ID id файла изображения.
     * @param int|bool $width ширина превью изображения. По умолчанию FALSE (не изменять размер).
     * @param int|bool $height высота превью изображения. По умолчанию FALSE (не изменять размер).
     * @param int $resizeType Тип масштабирования:
     *  BX_RESIZE_IMAGE_EXACT - масштабирует в прямоугольник $arSize c сохранением пропорций, обрезая лишнее;
     *  BX_RESIZE_IMAGE_PROPORTIONAL - масштабирует с сохранением пропорций, размер ограничивается $arSize;
     *  BX_RESIZE_IMAGE_PROPORTIONAL_ALT - масштабирует с сохранением пропорций за ширину при этом 
     *  принимается максимальное значение из высоты/ширины, размер ограничивается $arSize, 
     *  улучшенная обработка вертикальных картинок.
     * 
     * @return string  относительный путь к картинке, относительно DOCUMENT_ROOT. 
     */
    function KonturGetImageSrc($FILE_ID, $width=false, $height=false, $resizeType=BX_RESIZE_IMAGE_PROPORTIONAL)
    {
        $arFile=CFile::GetFileArray($FILE_ID);
        
        if(!empty($arFile) && $width && $height)
        {
            $arImage=CFile::ResizeImageGet($arFile, array('width'=>$width, 'height'=>$height), $resizeType);
            return empty($arImage) ? '' : $arImage['src'];
        }
        
        return empty($arFile) ? '' : $arFile['SRC'];
    }
}

