1) Подключить CRUD конфигурацю в /protected/config/crud.php
return [
	...
	'common.ext.parser.config.crud',
    'common.ext.iterator.config.crud',
]

3) Создать конфигурацию парсера по примеру common/ext/parser/config/parsers/example.php

2) Разместить кнопку запуска парсера (пример)
$myParserConfigId='application.config.parsers.myparser';
$controller->widget('\common\ext\parser\widgets\ParserButton', [
	'label'=>(\common\ext\parser\components\helpers\HParser::hasActiveProcess($myParserConfigId) ? 'Продолжить' : 'Запустить'),
    'config'=>$myParserConfigId,
    'tagOptions'=>['class'=>'pull-right'],
    'jsError'=>'$(".js-my-parser-btn").addClass("btn-danger").removeClass("btn-info");alert(cmsCommonExtIterator.getErrorMessage(response));',
    'jsDone'=>'$(".js-my-parser-btn").removeClass("btn-danger").addClass("btn-info");$(".js-btn-import").button("complete");return;',
    'htmlOptions'=>[
	    'title'=>'Запустить процесс',
        'data-complete-text'=>'Запустить',
        'data-loading-text'=>'Идет процесс, подождите...',
        'class'=>'btn btn-info pull-right js-my-parser-btn',
        'style'=>'margin-right:5px;margin-bottom:10px;width:220px'
    ],
    'progressOptions'=>['style'=>'display:none;width:97%;height:7px;top:36px;position:absolute;']
]);
