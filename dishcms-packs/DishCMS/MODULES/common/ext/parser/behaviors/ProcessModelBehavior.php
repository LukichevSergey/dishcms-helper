<?php
namespace common\ext\parser\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HTools;
use common\ext\parser\components\helpers\HStatus;
use common\ext\parser\components\helpers\HParser;
use common\ext\parser\components\exceptions\ProcessException;
use crud\models\ar\common\ext\parser\models\Parser;
use crud\models\ar\common\ext\parser\models\Group;
use crud\models\ar\common\ext\parser\models\Page;

class ProcessModelBehavior extends \CBehavior
{
    /**
     * Объект парсера
     * @var Parser
     */
    private $parser;
    
    /**
     * Scopes
     * @return []
     */
    public function scopes()
    {
        return [
            'byIsPeriodic'=>[
                'condition'=>'(`t`.`is_periodic` = 1)'
            ],
            'byIsNotPeriodic'=>[
                'condition'=>'(`t`.`is_periodic` != 1)'
            ]
        ];
    }
    
    /**
     * Scope: по хэшу процесса
     * @param integer $hash хэш процесса.
     * Если не задан, будет использовано значение текущего хэша процесса.
     * 
     * @return \crud\models\ar\common\ext\parser\models\Process
     */
    public function byConfigHash($hash=null)
    {
        if($hash === null) {
            $hash=$this->getConfigHash();
        }
        
        $criteria=new \CDbCriteria();
        
        $criteria->addColumnCondition(['config_hash'=>$hash]);
        
        $this->owner->getDbCriteria()->mergeWith($criteria);
        
        return $this->owner;
    }
    
    /**
     * Процесс готов к запуску.
     * Не проверяется статус процесса!
     * @return bool
     */
    public function isReady()
    {
        $readyTime=$this->getReadyTime();
        
        return !$readyTime || (time() >= $readyTime);        
    }
    
    /**
     * Получить время предстоящего запуска процесса.
     * @return int время предстоящего запуска процесса в формате Unix.
     * Если время предстоящего запуска находится в прошлом, возвращается 0 (нуль). 
     */
    public function getReadyTime()
    {
        $readyTime=0;
        
        if((bool)$this->owner->is_periodic) {
            $duration=(int)$this->owner->duration;
            $startTime=(int)date_create_from_format('Y-m-d H:i:s', $this->owner->start_time)->format('U');
            if(HTools::isDateEmpty($this->owner->last_execute_time)) {
                $lastExecuteTime=0;
            }
            else {
                $lastExecuteTime=(int)date_create_from_format('Y-m-d H:i:s', $this->owner->last_execute_time)->format('U');
            }
            $readyTime=($startTime + ($duration * floor((time() - $startTime) / $duration)));
            if($lastExecuteTime && ($lastExecuteTime > $readyTime) && $this->owner->isDone()) {
                $readyTime+=$duration;
            }
        }
        
        return $readyTime;
    }
    
    /**
     * Получить объект парсера
     * 
     * @return Parser
     */
    public function getParser()
    {
        if(!($this->parser instanceof Parser)) {
            throw new ProcessException('Парсер не найден.');
        }
        
        return $this->parser;
    }
    
    /**
     * Установить объект парсера
     * @param Parser $parser объект парсера
     * 
     * @return Process
     */
    public function setParser($parser)
    {
        $this->parser=$parser;
        
        return $this->owner;
    }
    
    /**
     * Получить конфигурацию парсера
     * 
     * @return \common\ext\parser\models\Config
     */
    public function getConfig()
    {
        return $this->getParser()->getConfig();
    }
    
    /**
     * Получить хэш процесса
     * 
     * @return string
     */
    public function getConfigHash()
    {
        return (string)$this->owner->config_hash;
    }
    
    /**
     * Инициализировать атрибут хэша конфигурации
     * 
     * @return Process 
     */
    public function initConfigHash()
    {
        $this->owner->config_hash=$this->getConfig()->getConfigHash();
        
        return $this->owner;
    }
    
    /**
     * Получить идентификатор процесса
     * 
     * @return int
     */
    public function getId()
    {
        return (int)$this->owner->id;
    }
    
    /**
     * Получить текущий номер итерации процесса
     * 
     * @return int
     */
    public function getIteration()
    {
        return (int)$this->owner->iteration;
    }
    
    /**
     * Получить количество страниц процесса
     * @param \CDbCriteria|[] $criteria дополнительный критерий выборки.
     * 
     * @return int
     */
    public function getPagesCount($criteria=null)
    {
        $criteriaGet=HDb::criteria();
        $criteriaGet->join='INNER JOIN ' . HDb::qt(Group::model()->tableName()) . ' AS `g` ON (`g`.`id`=`t`.`group_id` AND `g`.`process_id`=:processId)';
        $criteriaGet->params=['processId'=>$this->getId()];
        
        $criteriaGet->mergeWith(HDb::criteria($criteria));
        
        return (int)Page::model()->resetScope()->count($criteriaGet);
    }
    
    /**
     * Получить количество новых страниц
     * 
     * @return int
     */
    public function getNewPagesCount()
    {
        return $this->getPagesCount(['scopes'=>['byStatus'=>HStatus::NEWEST]]);
    }
    
    /**
     * Получить количество активных страниц
     * 
     * @return int
     */
    public function getActivePagesCount()
    {
        return $this->getPagesCount(['scopes'=>['byStatus'=>[[HStatus::NEWEST, HStatus::RUN]]]]);
    }
    
    /**
     * Получить количество обработанных страниц
     * 
     * @return int
     */
    public function getDonePagesCount()
    {
        return $this->getPagesCount(['scopes'=>['byStatus'=>HStatus::DONE]]);
    }
    
    /**
     * Получить процент завершенности процесса парсинга
     * 
     * @return int
     */
    public function getPercent()
    {
        $pagesCount=$this->getPagesCount();
        $pagesDoneCount=$this->getDonePagesCount();
        
        $percent=$pagesCount ? floor(100 * $pagesDoneCount / $pagesCount) : 100;
        
        if($this->owner->isNew() && !$pagesCount) {
            $percent=0;
        }
        elseif(($pagesCount == $pagesDoneCount) || ($percent > 100) || $this->owner->isDone()) {
            $percent=100;
        }
        
        return $percent;
    }
    
    /**
     * Обновить время последнего запуска процесса
     */
    public function updateLastExecuteTime()
    {
        $date=date('Y-m-d H:i:s');
        
        if($date != $this->owner->last_execute_time) {
            $this->owner->last_execute_time=$date;
            $this->owner->update(['last_execute_time']);
        }
    }

    /**
     * Сбросить статус процесса
     *
     * @return void
     */
    public function resetProcess()
    {
        $this->owner->changeStatus(HStatus::NEWEST);
        $this->clearGroups();
        $this->resetIteration();
    }
    
    /**
     * Запустить процесс парсинга
     */
    public function run()
    {
        set_time_limit(600); // 10 минут
        
        // увеличиваем счетчик итераций процесса
        $this->incIteration();
        
        // производится запуск нового процесса
        if($this->owner->isNew()) {
            // меняем статус процесса на "Запущен"
            $this->owner->changeStatus(HStatus::RUN);
            
            // создаем входную группу
            Group::model()->setProcess($this->owner)->createEntryGroup();
        }
        
        // запускаем процесс парсинга 
        $this->process();
    }

    /**
     * Удаление всех групп текущего процесса
     *
     * @return int кол-во удаленных групп
     */
    protected function clearGroups()
    {
        return Group::model()->resetScope()->deleteAllByAttributes(['process_id'=>$this->getId()]);
    }
    
    /**
     * Сбросить номер итерации процесса
     */
    protected function resetIteration()
    {
        if((int)$this->owner->iteration) {
            $this->owner->iteration=0;
            
            $this->owner->update(['iteration']);
        }
    }

    /**
     * Увеличить номер итерации процесса
     */
    protected function incIteration()
    {
        $this->owner->iteration=(int)$this->owner->iteration + 1;
        
        $this->owner->update(['iteration']);
    }
    
    /**
     * Получить страницы процесса
     * @param \CDbCriteria|[] $criteria дополнительный критерий выборки.
     * 
     * @return Page[]
     */
    protected function getPages($criteria=null)
    {
        $criteriaGet=HDb::criteria();
        $criteriaGet->order='`t`.`status`, `t`.`id`';
        $criteriaGet->join='INNER JOIN ' . HDb::qt(Group::model()->tableName()) . ' AS `g` ON (`g`.`id`=`t`.`group_id` AND `g`.`process_id`=:processId)';
        $criteriaGet->params=['processId'=>$this->getId()];
        
        $criteriaGet->mergeWith(HDb::criteria($criteria));
        
        if($pages=Page::model()->resetScope()->findAll($criteriaGet)) {
            $groups=Group::model()->resetScope()->byProcessId($this->getId())->findAll(['index'=>'id']);
        
            $_pages=[];
            foreach($pages as $page) {
                if($group=A::get($groups, $page->getGroupId())) {
                    $group->setProcess($this->owner);
                    $page->setGroup($group);
                }
                
                $_pages[$page->getId()]=$page;
            }
            
            return $_pages;
        }
        
        return [];
    }
    
    /**
     * Получить активные страницы процесса
     * @param \CDbCriteria|[] $criteria дополнительный критерий выборки.
     * 
     * @return Page[]
     */
    protected function getActivePages($criteria=null)
    {
        $criteriaGet=HDb::criteria();
        $criteriaGet->scopes=['byStatus'=>[[HStatus::NEWEST, HStatus::RUN]]];
        
        $criteriaGet->mergeWith(HDb::criteria($criteria));
        
        return $this->getPages($criteriaGet);
    }
    
    /**
     * Запустить процесс парсинга
     * 
     */
    protected function process()
    {
        if($this->owner->isRunned()) {
            $this->updateLastExecuteTime();
            
            if($pages=$this->getActivePages(['limit'=>$this->getConfig()->getLimit()])) {
                foreach($pages as $page) {
                    $page->process();
                }                
            }
            
            if(!$this->getActivePagesCount()) {
                $this->owner->changeStatus(HStatus::DONE);
            }
        }
        elseif(!$this->owner->isDone()) {
            $this->owner->changeStatus(HStatus::DONE);
        }
    }
}