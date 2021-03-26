<?php
/**
 * Тарифы СДЭК
 */
namespace cdek\models;

class Tariff
{
    use \common\traits\Singleton;
    
    /**
     * @const integer группа тарифов Интернет-Магазин.
     */
    const GROUP_IM=1;
    
    /**
     * @const integer группа тарифов Китайский экспресс.
     */
    const GROUP_CHINA_EXPRESS=10;
    
    /**
     * @const integer группа тарифов для обычной доставки.
     */
    const GROUP_DEFAULT=20;
    
    /**
     * @const integer режимы доставки "дверь-дверь".
     */
    const MODE_DD=1;
    
    /**
     * @const integer режимы доставки "дверь-склад".
     */
    const MODE_DS=2;
    
    /**
     * @const integer режимы доставки "склад-дверь".
     */
    const MODE_SD=3;
    
    /**
     * @const integer режимы доставки "склад-склад".
     */
    const MODE_SS=4;
    
    /**
     * Получить названия групп
     * @return array
     */
    public function groupLabels($group=false)
    {
        $labels=[
            self::GROUP_IM=>'Тарифы только для Интернет-магазина',
            self::GROUP_CHINA_EXPRESS=>'Тарифы Китайский экспресс',
            self::GROUP_DEFAULT=>'Тарифы для обычной доставки'
        ];
        
        if($group) {
            if(isset($labels[$group])) {
                return $labels[$group];
            }
            return false;
        }
        
        return $labels;
    }
    
    /**
     * Получить названия режимов доставки
     * @return array
     */
    public function modeLabels($mode=false)
    {
        $labels=[
            self::MODE_DD=>'Дверь-дверь',
            self::MODE_DS=>'Дверь-склад',
            self::MODE_SD=>'Склад-дверь',
            self::MODE_SS=>'Склад-склад'
        ];
        
        if($mode) {
            if(isset($labels[$mode])) {
                return $labels[$mode];
            }
            return false;
        }
        
        return $labels;
    }
    
    /**
     * Получить названия режимов доставки (публичная часть)
     * @return array
     */
    public function modePublicLabels($mode=false)
    {
        $labels=[
            //self::MODE_DD=>'',
            //self::MODE_DS=>'',
            self::MODE_SD=>'До адреса покупателя',
            self::MODE_SS=>'До пункта выдачи заказов (ПВЗ)'
        ];
        
        if($mode) {
            if(isset($labels[$mode])) {
                return $labels[$mode];
            }
            return false;
        }
        
        return $labels;
    }
    
    /**
     * Получить все тарифы доставки
     * Обновлено 12.11.2017
     * @return array
     */
    public function tariffs()
    {
        return [
            self::GROUP_IM=>[
                self::MODE_DD=>[
                    // Услуга: Посылка
                    139=>['title'=>'Посылка дверь-дверь (до 30 кг.)', 'maxWeight'=>30],
                    // Услуга: CDEK Express
                    293=>['title'=>'CDEK Express дверь-дверь']
                ],
                self::MODE_DS=>[
                    // Услуга: Посылка
                    138=>['title'=>'Посылка дверь-склад (до 30 кг.)', 'maxWeight'=>30],
                    // Услуга: InPost. 
                    // 3 вида ячеек: 
                    // А (8*38*64 см)— до 5 кг
                    // В (19*38*64 см) — до 7 кг
                    // С (41*38*64 см)— до 20 кг
                    301=>['title'=>'До постомата InPost дверь-склад'],
                    // Услуга: CDEK Express
                    295=>['title'=>'CDEK Express дверь-склад']
                ],
                self::MODE_SD=>[
                    // Услуга: Посылка
                    137=>['title'=>'Посылка склад-дверь (до 30 кг.)', 'maxWeight'=>30],
                    // Услуга: Экономичная посылка
                    233=>['title'=>'Экономичная посылка склад-дверь (до 50 кг.)', 'maxWeight'=>50],
                    // Услуга: CDEK Express
                    294=>['title'=>'CDEK Express склад-дверь']
                ],
                self::MODE_SS=>[
                    // Услуга: Посылка
                    136=>['title'=>'Посылка склад-склад (до 30 кг.)', 'maxWeight'=>30],
                    // Услуга: Экономичная посылка
                    234=>['title'=>'Экономичная посылка склад-склад (до 50 кг.)', 'maxWeight'=>50],
                    // Услуга: InPost. 
                    // 3 вида ячеек: 
                    // А (8*38*64 см)— до 5 кг
                    // В (19*38*64 см) — до 7 кг
                    // С (41*38*64 см)— до 20 кг
                    302=>['title'=>'До постомата InPost склад-склад'],
                    // Услуга: CDEK Express
                    291=>['title'=>'CDEK Express склад-склад']
                ]
            ],
            self::GROUP_CHINA_EXPRESS=>[
                self::MODE_DD=>[
                    245=>['title'=>'Китайский экспресс (дверь-дверь)']
                ],
                self::MODE_DS=>[
                    247=>['title'=>'Китайский экспресс (дверь-склад)']
                ],
                self::MODE_SD=>[
                    246=>['title'=>'Китайский экспресс (склад-дверь)']
                ],
                self::MODE_SS=>[
                    243=>['title'=>'Китайский экспресс (склад-склад)']
                ]
            ],
            self::GROUP_DEFAULT=>[
                self::MODE_DD=>[
                    // Услуга: Экспресс
                    1=>['title'=>'Экспресс лайт дверь-дверь (до 30 кг)', 'maxWeight'=>30],
                    // Услуга: Срочная доставка
                    3=>['title'=>'Супер-экспресс до 18'],
                    // Услуга: Экспресс
                    18=>['title'=>'Экспресс тяжеловесы дверь-дверь (от 30 кг)', 'minWeight'=>30],
                    // Услуга: Срочная доставка
                    57=>['title'=>'Супер-экспресс до 9 (до 5 кг)', 'maxWeight'=>5],
                    // Услуга: Срочная доставка
                    58=>['title'=>'Супер-экспресс до 10 (до 5 кг)', 'maxWeight'=>5],
                    // Услуга: Срочная доставка
                    59=>['title'=>'Супер-экспресс до 12 (до 5 кг)', 'maxWeight'=>5],
                    // Услуга: Срочная доставка
                    60=>['title'=>'Супер-экспресс до 14 (до 5 кг)', 'maxWeight'=>5],
                    // Услуга: Срочная доставка
                    61=>['title'=>'Супер-экспресс до 16']

                ],
                self::MODE_DS=>[
                    // Услуга: Экспресс
                    12=>['title'=>'Экспресс лайт дверь-склад (до 30 кг)', 'maxWeight'=>30],
                    // Услуга: Экспресс
                    17=>['title'=>'Экспресс тяжеловесы дверь-склад (от 30 кг)', 'minWeight'=>30]
                ],
                self::MODE_SD=>[
                    // Услуга: Экспресс
                    11=>['title'=>'Экспресс лайт склад-дверь (до 30 кг)', 'maxWeight'=>30],
                    // Услуга: Экспресс
                    16=>['title'=>'Экспресс тяжеловесы склад-дверь (от 30 кг)', 'minWeight'=>30]
                ],
                self::MODE_SS=>[
                    // Услуга: Экономичная доставка
                    5=>['title'=>'Экономичный экспресс склад-склад'],
                    // Услуга: Экспресс
                    10=>['title'=>'Экспресс лайт склад-склад (до 30 кг)', 'maxWeight'=>30],
                    // Услуга: Экспресс
                    15=>['title'=>'Экспресс тяжеловесы склад-склад (от 30 кг)', 'minWeight'=>30],
                    // Услуга: Экономичная доставка
                    62=>['title'=>'Магистральный экспресс склад-склад'],
                    // Услуга: Экономичная доставка
                    63=>['title'=>'Магистральный супер-экспресс склад-склад']
                ]
            ]
        ];
    }
    
    /**
     * Получить тарифы доставки
     * @param integer|false $group группа доставки. 
     * По умолчанию (false) все группы доставки.
     * @param integer|array|false $mode режим доставки.
     * По умолчанию (false) все режимы доставки.
     * @return array
     */
    public function tariffCodes($group=false, $mode=false)
    {
        if(!$group && !$mode) {
            return $this->tariffs();
        }
        
        $group=(int)$group;
        
        if($group) {
            $tariffs=$this->tariffs();
            if(isset($tariffs[$group])) {
                if(is_array($mode)) {
                    $result=[];
                    foreach($mode as $modeCode) { 
                        if(isset($tariffs[$group][(int)$modeCode])) {
                            $result[$group][$modeCode]=$tariffs[$group][(int)$modeCode];
                        }
                    }
                    return $result;
                }
                elseif($mode) { 
                    if(isset($tariffs[$group][(int)$mode])) {
                        return $tariffs[$group][(int)$mode];
                    }
                }
                else {
                    return $tariffs[$group];
                }
            }
        }
        elseif(is_array($mode)) {
            $result=[];
            foreach($this->tariffs() as $tariffGroup=>$tariffData) {
                foreach($mode as $modeCode) {
                    if(isset($tariffData[$modeCode])) {
                        $result[$tariffGroup][$modeCode]=$tariffData[$modeCode];
                    }
                }
            }
            return $result;
        }
        elseif($mode) {
            $result=[];
            foreach($this->tariffs() as $tariffGroup=>$tariffData) {
                if(isset($tariffData[(int)$mode])) {
                    $result[$tariffGroup][(int)$mode]=$tariffData[(int)$mode];
                }
            }
            return $result;
        }
        
        return [];
    }
    
    public function getTariffById($tariffId)
    {
        $tariffId=(int)$tariffId;
        foreach($this->tariffs() as $tariffGroup=>$tariffData) {
            foreach($tariffData as $mode=>$modeCode) {
                if(isset($modeCode[$tariffId])) {
                    return $modeCode[$tariffId];
                }
            }
        }
        return false;
    }
    
    public function tariffLabel($tariffId)
    {
        if($tariff=$this->getTariffById($tariffId)) {
            return $tariff['title'];
        }
        return false;
    }
    
    public function getTariffMode($tariffId)
    {
        $tariffId=(int)$tariffId;
        foreach($this->tariffs() as $tariffGroup=>$tariffData) {
            foreach($tariffData as $mode=>$modeCode) {
                if(isset($modeCode[$tariffId])) {
                    return $mode;
                }
            }
        }
        return false;
    }
}
