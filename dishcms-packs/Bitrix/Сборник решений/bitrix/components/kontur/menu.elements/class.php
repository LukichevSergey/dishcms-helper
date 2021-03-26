<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Kontur\Core\Iblock\ElementTable,
    Kontur\Core\Main\Tools;

class KonturMenuElementsComponent extends CBitrixComponent
{
	/**
	 * @param array $arParams массив параметров компонента
	 */
	public function getMenuItems($arParams)
    {
		$items=[];
        
        if($iblockId=Tools\Data::get($arParams, 'IBLOCK_ID')) {
        	$nameCode = Tools\Data::get($arParams, 'NAME_CODE', 'NAME');
        	$nameAlternativeCode = Tools\Data::get($arParams, 'NAME_ALTERNATIVE_CODE');
        	$select=['ID', 'CODE', $nameCode];
        	if($nameAlternativeCode) {
        		$select[]=$nameAlternativeCode;
        	}
            $elements=ElementTable::getAll([
                'filter'=>['IBLOCK_ID'=>$iblockId],
                'select'=>['ID', 'CODE', 'NAME']
            ]);
        
            if(!empty($elements)) {
                foreach($elements as $elm) {
                	$name=$elm[$nameCode];
                	if(empty($name) && $nameAlternativeCode) {
                		$name=$elm[$nameAlternativeCode];
                	}
                    $items[]=[
                        $name,
                        Tools\Data::get($arParams, 'SEF_BASE_URL', '') . $elm['CODE'] . '/',
                        [],
                        [],
                        ''
                    ];
                }
            }
        }
        
        return $items;
    }
}

