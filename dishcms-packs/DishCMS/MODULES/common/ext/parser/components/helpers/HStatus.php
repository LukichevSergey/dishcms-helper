<?php
namespace common\ext\parser\components\helpers;

class HStatus
{
    /**
     * Статус "Новый"
     * @var integer
     */
    const NEWEST=1;
    
    /**
     * Статус "Запущен"
     * @var integer
     */
    const RUN=2;
    
    /**
     * Статус "Завершен"
     * @var integer
     */
    const DONE=3;
    
    /**
     * Статус "Ошибка"
     * @var integer
     */
    const ERROR=4;
    
    /**
     * Статус "Запущен", производится получение ссылок
     * @var integer
     */
    const PROCESS_RUN_GETLINKS=201;
    
    /**
     * Статус "Запущен", производится получение контента
     * @var integer
     */
    const PROCESS_RUN_GETCONTENTS=202;
}