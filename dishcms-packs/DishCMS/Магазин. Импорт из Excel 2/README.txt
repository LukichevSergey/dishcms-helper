Импорт категорий и товаров.

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
