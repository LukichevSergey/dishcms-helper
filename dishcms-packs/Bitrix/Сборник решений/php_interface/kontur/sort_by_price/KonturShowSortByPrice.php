<?
if(!function_exists('KonturShowSortByPrice'))
{
    /**
     * Отображение пунктов сортировки по цене
     * @param array $arExtendSortOptions массив настроек дополнительных поле сортировки. Будут добавлены в конец списка.
     * Параметры:
     * 	"PARAM_NAME" - короткое имя переменной сортировки
     *  "TITLE" - название 
     *  "METHOD" - метод сортировки. "asc" или "desc". Если не задан (по умолчанию) "asc".
     *  "FILTER_PARAM_NAME" - имя свойства, как для фильтра (т.е. для свойств нужно передавать PROPERTY_<PROPERTY_CODE>).
     *  Необязательный, если совпадает с "PARAM_NAME".
     * @param string $template шаблон отображения.
     * По умолчанию "<a href="{href}" {selected}>{title}</a>"
     * Переменные шаблона:
     *  "{href}" - будет заменена на ссылку сортировки.
     *  "{selected}" - будет проставлен заменен переменной $selected у активного элемента.
     *  "{title}" - будет заменен заголовком по умолчанию.
     * @param string $selected код вставляемый у активного элемента.
     */
    function KonturShowSortByPrice($arExtendSortOptions=array(), $template='<a href="{href}" {selected}>{title}</a>', $selected='class="selected"')
    {
    	global $APPLICATION;

    	$sRequestSort=empty($_REQUEST['sort']) ? null : $_REQUEST['sort'];
    	$sRequestMethod=empty($_REQUEST['method']) ? null : $_REQUEST['method'];
        $arRemoveParams=array('SHOWALL_1', 'sort', 'method');
        $arSortOptions=array(
            array(
                'PARAM_NAME'=>'price',
                'TITLE'=>'возрастанию цены',
                'FILTER_PARAM_NAME'=>'PROPERTY_MAXIMUM_PRICE'
            ),
            array(
                'PARAM_NAME'=>'price',
                'METHOD'=>'desc',
                'TITLE'=>'убыванию цены',
                'FILTER_PARAM_NAME'=>'PROPERTY_MAXIMUM_PRICE'
            )
        );
        $arSortOptions=array_merge($arSortOptions, $arExtendSortOptions);
        foreach($arSortOptions as $i=>$arOption) {
            $sMethod=empty($arOption['METHOD']) ? 'asc' : $arOption['METHOD'];
            $bSelected=in_array($sRequestSort, array($arOption['PARAM_NAME'], $arOption['FILTER_PARAM_NAME'])) && ($sRequestMethod==$sMethod);
            $arTemplateParams=array(
                '{href}'=>$APPLICATION->GetCurPageParam('sort='.$arOption['PARAM_NAME'].'&method='.$sMethod, $arRemoveParams),
                '{selected}'=>($bSelected ? $selected : ''),
                '{title}'=>$arOption['TITLE']
            );
            echo strtr($template, $arTemplateParams) . (($i+1)<count($arOption) ? ',' : '');
        }
    }
}
