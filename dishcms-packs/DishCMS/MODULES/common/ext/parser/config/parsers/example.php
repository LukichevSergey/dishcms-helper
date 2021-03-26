<?php
/**
 * 
 * Пример конфигурации парсера
 *
 
 *** Передача строки как тип "callable" запрещена.

Процесс парсинга происходит в два этапа:
1 этап) Получение всех ссылок на страницы для парсинга, включая страницы пагинации
2 этап) Парсинг контента полученных страниц.
   
Структура конфигурации:
   
"domain" => (string) основной адрес домена для относительных ссылок
   
"entry" => (string) URL страницы входа. Может быть задана, как относительная ссылка для домена
   
"limit" => (integer) количество страниц обрабатываемых за одну итерацию. По умолчанию 10.

"delay" => (integer) задержка между запросами к серверу с которого происходит парсинг (в секундах). 
По умолчанию 0 (нуль) задержки нет.
   
"iterator" => (array) конфигурация для итератора. Подробнее в описание конфигурации расширения \common\ext\iterator
По умолчанию будет использована предустановленная конфигурация.


"save" => (callable) переопределение основного обработчика сохранения полученных данных.
Определяется как function($page, $data) { return boolean }, где
    @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
    @param $data (array) массив данных для сохранения где каждый элемент это массив array(attribute=>value).
    @return boolean функция должна возвращать true в случае успешного сохранения данных 
   
"groups" => (array) группы подконфигураций
   
Группа конфигурации задается следующим образом:

"символьный_код_группы" => array(

    *** В конфигурации группы могут быть не заданы параметры "content", "links" или "pagination" 
    если, например, требуется только формальное разделение на подгруппы. 
        
    "precontent" => (array|callable) основная предобработка контента страницы перед разбором.
    Обработанный в этом параметре контент будет передан во все подразделы группы и подгруппы.
    Варианты принимаемых значений:
        - (array) может быть передан массив array("preg_replace", pattern, replace)
            pattern (string) паттерн для функции preg_replace();
            replace (string|callback) параметр repalce для функции preg_replace();
                Если передан callback, то будет возвращен результат функции preg_replace_callback();
        
        - (array) может быть передан массив array("dom", find_selector, inner)
            find_selector (string) подробнее \simple_html_dom_node->find();
            inner (bool) получить только внутреннее содержимое. По умолчанию (false) будет возвращено
            содержимое вместе с родительским элементом (outertext).
          
        - (callable) функция вида function($page, $content) { return string }
            @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
            @param $content (string) контент страницы
            @return string функция должна возвращать обработанное содержимое страницы для дальнейшего разбора.
        
        
    "recursive" => (boolean) рекурсивный парсинг. По умолчанию (false) группа обрабатывается только один раз, далее идет 
        обработка только подгрупп. Если установлено в (true) для вложенных страниц будет повторен поиск вложенных страниц. 
        Рекурсивный парсинг может потребоваться если происходит парсинг каталога товаров, а ссылки на подкатегории каталога 
        появляются только при нахождении в родителькой категории. И может не потребоваться (ускорить быстродействие), если
        заранее известно, что все необходимые ссылки на внутренние страницы (включая страницы пагинации) могут быть получены
        с одной входной для группы страницы.
        
    "content" - это конфигурация разбора данных текущей страницы для записи в базу данных
    "content" => array(
            "tablename" => (string) имя таблицы в базе данных в которую будут сохраняться распарсенные данные.
            или
            "model" => (string) класс модели \CActiveRecord для записи данных. В этом случае, имя таблицы 
            для сохранения распарсенных данных будет получена через метод tableName().
            
            "sync_attribute" => (string) имя поля в таблице базы данных со значением хэша, по которому будет происходить синхронизация данных.
            По умолчанию будет добавлено новое поле "parser_sync_hash" (UNIQUE KEY).
            
            "syncs" => (callable|string|array) имя атрибута или массив атрибутов вида array(attribute), из значений 
                которых будет генериться хэш синхронизации. Может быть передан тип callable в котором указывается
                обработчик получения хэша записи для синхронизации. Функция должна быть вида function($page, $item) { return string|array; }
                @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
                @param $item (array) массив данных записи
                @return string|array фунция должна возвращать данные для генерации уникального хэша записи 
                по которому будет в дальнейшем происходить синхронизация.
                
            "required" => (array) массив атрибутов вида array(attribute), которые обязаны иметь не пустые значения.
                Исключение составляет числовое значение 0 (нуль)
            
            "precontent" => (array|callable) (необязательно) дополнительная предобработка содержимого страницы перед разбором.
            Задается аналогочино основному параметру "precontent".
            
            
            "pattern" => (string|array|callable) универсальный шаблон получения блока одного элемента.
            Варианты принимаемых значений:
                - (string) паттерн для функции preg_match_all(PREG_PATTERN_ORDER), блоки будут сформированы из $matches[1];
                
                - (array) может быть передан массив array("dom", find_selector, inner)
                    find_selector (string) подробнее \simple_html_dom_node->find();
                    inner (bool) получить только внутреннее содержимое. По умолчанию (false) будет возвращено
                    содержимое вместе с родительским элементом (outertext).
                    
                - (callable) функция вида function($page, $content) { return array }
                    @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
                    @param $content (string) контент страницы
                    @return array функция должна возвращать массив блоков кода для дальнейшего разобра.
            
            
            "attributes" => (array|callable) шаблоны для получения значений полей элемента. 
                Варианты принимаемых значений:
                - (array) массив вида array(attribute => value), где 
                    "attribute" - (string) имя поля в таблице базы данных
                    "value" - (string|array|callable|\CDbExpression) паттерн получения значения атрибута.
                    Варианты принимаемых значений параметра "value":
                        - (\CDbExpression) объект SQL выражения
                        
                        - (string) паттерн для функции preg_match(), значение будет получено из $matches[1];
                        
                        - (array) может быть передан массив array("preg_replace", pattern, replace)
                            pattern (string) паттерн для функции preg_replace();
                            replace (string|callback) параметр repalce для функции preg_replace();
                                Если передан callback, то будет возвращен результат функции preg_replace_callback();
                
                        - (array) может быть передан массив array("dom", find_selector, attribute)
                            find_selector (string) подробнее \simple_html_dom_node->find();
                            attribute (string) имя атрибута DOM элемента, если в качестве значения необходимо получить его содержимое.
                                Если не передано, то в качестве значения будет получено внутреннее содержимое элемента.
                            
                        - (callable) функция вида function($page, $blockContent) { return string }
                            @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
                            @param $blockContent (string) контент блока
                            @return string значение атрибута
                    
                    
                - (callable) функция вида function($page, $blockContent) { return array }
                    @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
                    @param $blockContent (string) контент блока
                    @return array функция должна возвращать массив атрибутов со значениями вида array(attribute=>string).
            
            
            "beforeSave" => (callable) предобработка данных перед сохранением
                Задается как function($page, &$data) { return array }
                @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
                @param &$data (array) массив элементов для сохранения, где каждый элемент это массив атрибутов вида array(attribute=>value).
                @return array модифицированный массив данных для сохранения
                
            "onDublicateSQL" => (string) SQL выражение для обновления существующих записей для запроса INSERT ... ON DUPLICATE KEY UPDATE 
                
            "save" => (callable) переопределение основного метода сохранения данных.
                Задается как function($page, $data) { return boolean }
                    @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
                    @param $data (array) массив элементов для сохранения, где каждый элемент это массив атрибутов вида array(attribute=>value).
                    @return boolean функция должна возвращать true в случае успешного сохранения данных 
       )
       
       "links" - это конфигурация разбора ссылок на внутренние страницы для дальнейшего их разбора 
       "links" => array(
            "precontent" => (array|callable) (необязательно) дополнительная предобработка содержимого страницы перед разбором.
            Задается аналогочино основному параметру "precontent".
            
            "pattern" => (string|array|callable) универсальный шаблон получения ссылок.
            Варианты принимаемых значений:
                - (string) паттерн для функции preg_match_all(PREG_PATTERN_ORDER), ссылки будут сформированы из $matches[1];
                
                - (array) может быть передан массив array("dom", find_selector, attribute)
                    find_selector (string) подробнее \simple_html_dom_node->find();
                    attribute (string) имя атрибута DOM элемента, если в качестве значения необходимо получить его содержимое.
                        Если не передано, то в качестве значения будет получено внутреннее содержимое элемента.
                        По умолчанию "href". Чтобы передать пустое значение, необходимо передать пустую строку "".
                    
                - (callable) функция вида function($page, $content) { return array }
                    @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
                    @param $content (string) контент страницы
                    @return array функция должна возвращать массив ссылок для дальнейшего разобра.
                    
            "beforeSave" => (callable) предобработка ссылок перед сохранением
                Задается как function($page, &$links) { return array }
                @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
                @param &$links (array) массив элементов ссылок для сохранения, где каждый элемент это массив атрибутов вида array(attribute=>value).
                @return array модифицированный массив ссылок для сохранения
                
            "save" => (callable) переопределение основного метода сохранения данных.
                Задается как function($page, $links) { return boolean }
                    @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
                    @param $links (array) массив элементов ссылок для сохранения, где каждый элемент это массив атрибутов вида array(attribute=>value).
                    @return boolean функция должна возвращать true в случае успешного сохранения данных 
           
        )
        
        "pagination" - это конфигурация разбора ссылок пагигатора на последующие страницы 
        "pagination" => array(
            "precontent" => (array|callable) (необязательно) дополнительная предобработка содержимого страницы перед разбором.
            Задается аналогочино основному параметру "precontent".
            
            "pattern" => (string|array|callable) универсальный шаблон получения ссылок пагинатора.
            Варианты принимаемых значений:
                - (string) паттерн для функции preg_match_all(PREG_PATTERN_ORDER), ссылки пагинатора будут сформированы из $matches[1];
                
                - (array) может быть передан массив array("dom", find_selector, attribute)
                    find_selector (string) подробнее \simple_html_dom_node->find();
                    attribute (string) имя атрибута DOM элемента, если в качестве значения необходимо получить его содержимое.
                        Если не передано, то в качестве значения будет получено внутреннее содержимое элемента.
                        По умолчанию "href". Чтобы передать пустое значение, необходимо передать пустую строку "".
                    
                - (callable) функция вида function($page, $content) { return array }
                    @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
                    @param $content (string) контент страницы
                    @return array функция должна возвращать массив ссылок пагинатора для дальнейшего разобра.
         
           "beforeSave" => (callable) предобработка ссылок пагинатора перед сохранением
                Задается как function($page, &$links) { return array }
                @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
                @param &$links (array) массив элементов ссылок пагинатора для сохранения, где каждый элемент это массив атрибутов вида array(attribute=>value).
                @return array модифицированный массив ссылок пагинатора для сохранения
                
            "save" => (callable) переопределение основного метода сохранения данных.
                Задается как function($page, $links) { return boolean }
                    @param $page (\crud\models\ar\common\ext\parser\models\Page) модель текущей страницы для которой были получены данные.
                    @param $links (array) массив элементов ссылок пагинатора для сохранения, где каждый элемент это массив атрибутов вида array(attribute=>value).
                    @return boolean функция должна возвращать true в случае успешного сохранения данных 
        )
        
        "groups" - это конфигурация подгрупп           
        "groups" => (array) задается аналогично основному параметру "groups".
   )


 */
return [
    'domain'=>'http://example.com',
    
    'entry'=>'/',
    
    'save'=>function($group, $type, $data) {
        
    },
    
    'groups'=>[  
        
        'group1'=>[
            'precontent'=>['dom', 'body'],
            
            'content'=>[
                'precontent'=>['dom', 'body div.content'],                
                'pattern'=>['dom', 'div.content div.item'],                
                'attributes'=>[
                    'attribute1'=>['dom', 'div.item div.title'],
                    'attribute2'=>['dom', 'div.item a', 'href'],
                ]
            ],
            
            'links'=>[
                'precontent'=>['dom', 'body div.content div.item'],
                'pattern'=>['dom', 'body div.content input.btn', 'data-src']
            ],
            
            'pagination'=>[
                'precontent'=>['dom', 'body div.content div.pagination ul li a'],
                'pattern'=>['dom', 'a']
            ]
        ],      
    ]
];
