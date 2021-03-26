-------------------------------------------------------------------
--
-- Подключение модуля обмена 
-- (ecommerce / exchange)
--
-------------------------------------------------------------------

ВАЖНО!

После копирования файлов из папки install
Удалите не нужные конфигурации в папке protected\config\exchanges\

Конфигурации в папке protected\config\exchanges\ может потребоваться 
дорабатывать для конкретных задач сайта.

-------------------------------------------------------------------

1) Скопировать файлы из папки install

2) Добавить в protected\config\crud.php (если еще не добавлено)

return [
    ...
    'common.ext.iterator.config.crud'
];

3) Добавить в protected\config\events.php

use common\components\helpers\HArray as A;

return [
    ...
    'onCommonExtIteratorGetSecureKeys'=>function($event) {
        $event->params['secures']=A::m(A::get($event->params, 'secures', []), [
            \ecommerce\modules\exchange\models\ExcelImport::secure(),
            \ecommerce\modules\exchange\models\ExcelExport::secure()
        ]);
    }
];

4) Добавить модуль в protected/modules/ecommerce/config/main.php

return [
    ...
    'modules'=>[
        ...
        'exchange'=>['class'=>'\ecommerce\modules\exchange\ExchangeModule'],
    
    
5) Добавьте нужные пункты меню в раздел администрирования

[
    'active'=>Y::isAction(Y::controller(), 'exchange', 'import'),
    'label'=>'Импорт из Excel', 
    'url'=>['exchange/import']
],
[
    'active'=>Y::isAction(Y::controller(), 'exchange', 'export'),
    'label'=>'Экспорт в Excel', 
    'url'=>['exchange/export']
],