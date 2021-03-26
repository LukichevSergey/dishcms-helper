Если нужно использовать внутри комплексного компонента (например страница детальной информации о товаре), можно:
1) дополнительно передать в параметрах компонента $arResult и $arParams
<?$APPLICATION->IncludeComponent(
	"kontur:content.tabs", 
	"my_template", 
	array(
        ...
		"AR_RESULT" => $arResult,
		"AR_PARAMS" => $arParams
	),
	false
);?>

2) В шаблоне отображения "my_template" (template.php) сделать вывод
<? include($_SERVER['DOCUMENT_ROOT'] . $arTab['FILE']); ?>

3) В файле вкладки использовать соотвественно
$arParams['AR_RESULT'] и $arParams['AR_PARAMS']

