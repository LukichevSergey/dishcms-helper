<?php
/**
 * Языковой файл перевода для \extend\modules\regions\behaviors\RegionAttributeBehavior
 */
return [
	'label.use_default' => '<span style="position:relative;display:inline-block;z-index:9" title="Использовать значение региона по умолчанию"'
		. ' onmouseover="$(this).find(\'> div\').show()" onmouseout="$(this).find(\'> div\').hide()">({default_title})'
		. '<div style="display:none;position:absolute;right:0;background:#f5f5f5;padding:3px 5px;border:1px solid #aaa;color:#000;text-align:left;border-radius:3px;"">'
		. '<div style="border-bottom:1px solid #aaa;margin-bottom:3px;padding:2px;font-weight:bold;">Значение региона {default_title}:</div>{default_value}</div></span>',
	'label.is_forced'=> 'Принудительно использовать установленное значение (в том числе пустое)',
	'label.use_default.title'=> 'Использовать значение региона по умолчанию ({default_title})',
	'value.emptyText' => 'Значение не задано',
	'value.emptyTextOrOrigin' => 'Значение не задано, либо используется оригинал',
	'error.regionChanged'=>'Во время редактирования текущий регион был изменен. Сохранение не выполнено.'
];