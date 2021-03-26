<?php
/**
 * Конфигурация импорта товаров из FrontPad
 */
 
return [
    /**
     * Конфигурация для сайта
     */
    'site'=>[
        /**
         * Разделы для импорта.
         * Варианты значений
         * [(string)=>[array]] "ключ" массива является строковым значением, значением является "массив", 
         * то "ключ" будет названием раздела с подразделами, указанными в "массиве" значения.
         * [(string)=>(integer)] "ключ" массива является строковым значением, значением является "число",
         * то "ключ" будет названием раздела, "число" это идентификатор инфоблока из которого будут отображены товары.
         * [(integer)=>true] "ключ" массива является числом, значением является булевое "TRUE",
         * то "ключ" это идентификатор инфоблока (название раздела будет является наименованием данного инфоблока),
         * "TRUE" - указывает на то, что должны отобразиться разделы первого уровня указанного инфоблока, как подразделы.
         * [(integer)] указано только значение, которое является "числом", то "число" - это идентификатор инфоблока,
         * который будет отображен как раздел. Товары будут отображены все, включая товары во всех подразделах данного инфоблока.
         * 
         */
        'sections'=>[
            'Каталог'=>[
                3=>true,
                2=>true,
                4=>true
            ],
            'Конструктор'=>[
                6,
                7,
                8=>true,
                9
            ]
        ],
        
        /**
         * Дополнительные свойства товара (характеристики)
         */
        'properties'=>[
            /**
             * Свойства артикул FrontPad
             * [iblock_id=>[
             *  код_свойства_товара=>имя_свойства_как_артикул_FrontPad,
             *  код_свойства_товара, в таком объявлении свойство будет установлено, как основное свойство товара для артикула FrontPad
             * ]]
             */
            'frontpad'=>[
                3=>[
                    'FRONTPAD_CODE',
                    'BD_PROPS_1'=>'FRONTPAD',
                    'BD_PROPS_2'=>'FRONTPAD'
                ],
                2=>['FRONTPAD_CODE'],
                4=>['FRONTPAD_CODE'],
                6=>['FRONTPAD_CODE'],
                7=>['FRONTPAD_CODE'],
                8=>['FRONTPAD_CODE'],
                9=>['FRONTPAD_CODE']
            ]
        ],
        
        /**
         * Настройки цен товаров
         * 
         * свойства товаров цен вида [iblockId=>propertyPriceCode], 
         * либо [iblockId=>["PRICE"=>propertyPriceCode]]
         * для обновления цены в сложном типе свойства "PROPERTIES"
         */
        'prices'=>[
            3=>[
                'PRICE'=>'PRICE', 
                'PROPERTIES'=>[
                    'BD_PROPS_1'=>'PRICE', 
                    'BD_PROPS_2'=>'PRICE'
                ],
            ],
            4=>'PRICE',
            6=>'PRICE',
            7=>'PRICE',
            8=>'PRICE',
            9=>'PRICE',
        ],
        
        /**
         * Конфигурация для товаров сайта
         */
        'products'=>[
            // выражение выборки товаров
            'select'=>['ID', 'IBLOCK_ID', 'SECTION_ID', 'NAME']
        ]
    ],
    /**
     * Конфигурация для FrontPad
     */
    'frontpad'=>[
        // API ключ
        'apikey'=>'bZA2nRrAYGQeS5D49dKe8FhTKEBYe9ehZF7ahiA6Tb9B6Ds5G49Fzfk7NQRTNZ4As2AD92yT4yhYZBQtsN2QY9KyQrbnb6QtSaYS3eARBGBk8bGnRt5AtDY7SZiYR2bz32dQ5Dtt9TG7ZbiBzb3RFHNktZ79K8zAYBbQ5z84dFZ3G7r69dHENiR78K7GHs7tT97Ky6DAAkn7hF4fHBNiKreG4bdAy9Sb9QQtGdT7s85BDTHafhiTBsnrtt',
        // фильтр товаров выгружаемых из FrontPad
        'filter'=>function($code) {
            return ((int)$code > 200000);
        },
        // точка продаж для нового заказа
        'point'=>513,
        // статусы заказа
        'order_statuses'=>[
            0=>'Новый', // new
            6=>'В работе', // kitchen
            1=>'Готов', // treated
            2=>'Списан', // denied
            4=>'Списан', // not_payed 
            3=>'Новый', // payed
            5=>'Новый', // wait_pay
            7=>'Выполнен', // delivery
            8=>'В пути', // courier 
        ],
        // статусы заказа, которые завершены
        'order_done_statuses'=>[
            2,
            4,
            7
        ],
        'payment'=>[
            'iblock'=>18,
            'property'=>'FRONTPAD_PAY_ID'
        ],
        'worktime'=>[
            'from'=>[10, 0],
            'to'=>[23, 30],
            'timezone'=>-0
        ]
    ],
    
    'bdpizza'=>[
        'pickup'=>17
    ]
];
