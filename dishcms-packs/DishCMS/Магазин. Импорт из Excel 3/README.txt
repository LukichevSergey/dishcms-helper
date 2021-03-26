ВАЖНО! Для корректной выгрузки дополнительных атрибутов товара необходимо, 
чтобы у таблицы eav_value был установлен UNIQUE unq_attr_product (id_attrs, id_product)
"unq_attr_product" - это произвольное имя индекса

Импорт категорий и товаров (с возможность выгрузки дополнительных атрибутов).

1) Скопировать файлы из папок protected и uploads
2) Установить права для записи на папки /uploads/xlsimport и /protected/runtime/xlsimport
3) Добавить пункт в меню раздела администрирования /protected/modules/admin/config/menu.php

[
   	'active'=>Y::isAction(Y::controller(), 'xlsimport'),
    'label'=>'Импорт каталога (XLS)',
    'url'=>['xlsimport/index']
],

-----

Настройка данных здесь /protected/modules/admin/models/XlsImportForm.php::import()

-----

В папке !examples пример файла загрузки.
