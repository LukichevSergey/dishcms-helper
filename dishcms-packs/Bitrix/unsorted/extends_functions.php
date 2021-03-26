/**
 * Получить значение элемента массива
 * Пример получения значения для $arResult: getArrayValue($arResult, 'SECTION.NAME');
 * @param array $array массив
 * @param mixed $key ключ массива, может быть передан также массив ключей в глубину, либо строка ключей разделенных точкой.
 * @param mixed $default значение возвращаемое по умолчанию, если элемент пуст или не найден.
 */
function getArrayValue($array, $key, $default=null) {
	if(is_string($key) && (strpos($key, '.') !== false)) $key=explode('.', $key);
	if(is_array($key)) {
		$k=array_shift($key);
		if(is_array($array[$k])) return getArrayValue($array[$k], $key, $default);
		$key=$k;
	}
	return !empty($array[$key]) ? $array[$key] : $default;
}

/**
 * Получение пользовательского свойства
 * @param string $IBLOCK_ID идентификатор инфоблока
 * @param string $SECTIN_ID идентификатор раздела
 * @param string $PROPERTY_NAME имя пользовательского свойства без префикса "UF_".
 * @return mixed Если свойство не найдено, возвращается NULL.
 */
function getUFProperty($IBLOCK_ID, $SECTIN_ID, $PROPERTY_NAME)
{
	$ar_result=CIBlockSection::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "ID"=>$SECTIN_ID),false, Array("UF_{$PROPERTY_NAME}"));
	return ($res=$ar_result->GetNext()) ? $res["UF_{$PROPERTY_NAME}"] : null;
}

// Пример использования

// установка заголовка браузера и H1
$IBLOCK_ID=getArrayValue($arResult, 'SECTION.IBLOCK_ID');
$SECTION_ID=getArrayValue($arResult, 'SECTION.ID');
$set=function($name, $uf, $empty=false) use ($IBLOCK_ID, $SECTION_ID, &$APPLICATION) {
	$value=getUFProperty($IBLOCK_ID, $SECTION_ID, $uf);
	if($empty || !empty($value)) $APPLICATION->SetPageProperty($name, $value?:'');
};
$set('uf_seo_title', 'SEO_TITLE');
$set('uf_seo_h1', 'SEO_H1');
$set('keywords', 'SEO_KEY', true);
$set('description', 'SEO_DESC', true);