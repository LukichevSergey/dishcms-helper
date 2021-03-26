<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class KonturMediaLibraryGalleryComponent extends CBitrixComponent
{
    public function getCollectionItems($arParams)
    {
        $items=[];
        
        if(!empty($arParams['MLIBCOLLECTION_ID'])) {
            \Bitrix\Main\Loader::includeModule('fileman');
            \CMedialib::Init();
            
            $items=\CMedialibItem::GetList(array('arCollections' => array("0" => $arParams['MLIBCOLLECTION_ID'])));
        }
        
        return $items;
    }
}