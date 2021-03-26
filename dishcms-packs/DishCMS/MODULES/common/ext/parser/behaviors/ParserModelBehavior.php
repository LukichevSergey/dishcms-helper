<?php
namespace common\ext\parser\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HDb;
use common\ext\parser\components\helpers\HStatus;
use common\ext\parser\models\Config;
use common\ext\parser\components\exceptions\ParserException;
use crud\models\ar\common\ext\parser\models\Process;
use crud\models\ar\common\ext\parser\models\Group;
use crud\models\ar\common\ext\parser\models\Page;
use crud\models\ar\common\ext\parser\models\Content;

class ParserModelBehavior extends \CBehavior
{
    /**
     * Конфигурация парсера
     * @var Config
     */
    private $config;
    
    /**
     * Получить конфигурацию парсера
     * @return \common\ext\parser\models\Config
     */
    public function getConfig()
    {
        if(!($this->config instanceof Config)) {
            throw new ParserException('Конфигурация парсера не найдена.');
        }
        
        return $this->config;
    }
    
    /**
     * Установить конфигурацию парсера
     * 
     * @param Config $config
     * 
     * @return Parser
     */
    public function setConfig($config)
    {
        $this->config=$config;
        
        return $this->owner;
    }
    
    /**
     * Загрузить конфигурацию парсера из хэша конфигурации
     * @param string $configHash хэш конфигурации
     */
    public function loadConfigByHash($configHash)
    {
        $this->config=Config::loadByHash($configHash);
    }
    
    /**
     * Получить непериодический активный процесс для указанной конфигурации
     * 
     * @return Process
     */
    public function getActiveProcess()
    {
        return Process::model()
            ->resetScope()
            ->byIsNotPeriodic()
            ->setParser($this->owner)
            ->initConfigHash()
            ->byConfigHash()            
            ->byNotStatus([HStatus::DONE, HStatus::ERROR])
            ->find();
    }
    
    /**
     * Получить непериодический активный процесс для указанной конфигурации
     *
     * @return Process
     */
    public function getPeriodicActiveProcess()
    {
        $process=Process::model()
            ->resetScope()
            ->byIsPeriodic()
            ->setParser($this->owner)
            ->initConfigHash()
            ->byConfigHash()
            ->byNotStatus([HStatus::DONE, HStatus::ERROR])
            ->find();
        
        if(!$process) {
            $process=Process::model()
                ->resetScope()
                ->byIsPeriodic()
                ->setParser($this->owner)
                ->initConfigHash()
                ->byConfigHash()
                ->byStatus([HStatus::DONE])
                ->find();
        }
        
        if($process && !$process->isReady()) {
            $process=null;
        }
        
        return $process;
    }
    
    /**
     * Получить хэш процесса
     * @return string
     */
    public function getProcessHash()
    {
        return md5($this->getConfig()->getFilename());
    }
    
    /**
     * Получить процесс по хэшу процесса
     * @return Process
     */
    public function getProcessByProcessHash()
    {
        return Process::model()
            ->resetScope()
            ->byIsNotPeriodic()
            ->setParser($this->owner)
            ->initConfigHash()
            ->byConfigHash()
            ->findByAttributes(['process_hash'=>$this->getProcessHash()]);
    }
    
    /**
     * Получить периодический процесс по хэшу процесса
     * @return Process
     */
    public function getPeriodicProcessByProcessHash()
    {
        return Process::model()
            ->resetScope()
            ->byIsPeriodic()
            ->setParser($this->owner)
            ->initConfigHash()
            ->byConfigHash()
            ->findByAttributes(['process_hash'=>$this->getProcessHash()]);
    }
    
    /**
     * Получить активый процесс по хэшу процесса
     * @return Process
     */
    public function getActiveProcessByProcessHash()
    {
        return Process::model()
            ->resetScope()
            ->byIsNotPeriodic()
            ->setParser($this->owner)
            ->initConfigHash()
            ->byConfigHash()
            ->byNotStatus([HStatus::DONE, HStatus::ERROR])
            ->findByAttributes(['process_hash'=>$this->getProcessHash()]);
    }
    
    /**
     * Получить активый регулярный процесс по хэшу процесса
     * @return Process
     */
    public function getActivePeriodicProcessByProcessHash()
    {
        $process=Process::model()
            ->resetScope()
            ->byIsPeriodic()
            ->setParser($this->owner)
            ->initConfigHash()
            ->byConfigHash()
            ->byNotStatus([HStatus::DONE, HStatus::ERROR])
            ->findByAttributes(['process_hash'=>$this->getProcessHash()]);
        
        if(!$process) {
            $process=Process::model()
                ->resetScope()
                ->byIsPeriodic()
                ->setParser($this->owner)
                ->initConfigHash()
                ->byConfigHash()
                ->byStatus([HStatus::DONE])
                ->findByAttributes(['process_hash'=>$this->getProcessHash()]);
        }
        
        if($process && !$process->isReady()) {
            $process=null;
        }
        
        return $process;
    }
    
    /**
     * Получить завершенный процесс по хэшу процесса.
     * Получает последний завершенный процесс.
     * @return Process
     */
    public function getDoneProcessByProcessHash()
    {
        return Process::model()
            ->resetScope()
            ->byIsNotPeriodic()
            ->setParser($this->owner)
            ->initConfigHash()
            ->byConfigHash()
            ->byStatus([HStatus::DONE, HStatus::ERROR])
            ->findByAttributes(['process_hash'=>$this->getProcessHash()], ['order'=>'create_time DESC']);
    }
    
    /**
     * Получить завершенный периодический процесс по хэшу процесса.
     * @return Process
     */
    public function getDonePeriodicProcessByProcessHash()
    {
        return Process::model()
            ->resetScope()
            ->byIsPeriodic()
            ->setParser($this->owner)
            ->initConfigHash()
            ->byConfigHash()
            ->byStatus([HStatus::DONE, HStatus::ERROR])
            ->findByAttributes(['process_hash'=>$this->getProcessHash()], ['order'=>'create_time DESC']);
    }
    
    /**
     * Создать новый процесс
     * @throws ParserException
     * @return \crud\models\ar\common\ext\parser\models\Process
     */
    protected function createProcess()
    {
        $process=new Process;
        $process->setParser($this->owner);
        $process->initConfigHash();
        $process->process_hash=$this->getProcessHash();
        $process->pid=(int)getmypid();
        
        if(!$process->save()) {
            throw new ParserException('Не удалось создать процесс.');
        }
        
        return $process;
    }
    
    /**
     * Создать новый периодический процесс
     * @param int $startTime дата и время первого запуска процесса в формате Unix.
     * По умолчанию 0 (нуль) текущее дата и время.
     * @param int $duration интервал повторного запуска процесса в секундах.
     * По умолчанию 86400 (сутки)
     * @throws ParserException
     * @return \crud\models\ar\common\ext\parser\models\Process
     */
    protected function createPeriodicProcess($startTime=0, $duration=86400)
    {
        $process=new Process;
        $process->setParser($this->owner);
        $process->initConfigHash();
        $process->process_hash=$this->getProcessHash();
        $process->pid=(int)getmypid();
        $process->start_time=date('Y-m-d H:i:s', (int)$startTime ?: time());
        $process->duration=(int)$duration;
        $process->is_periodic=1;
        
        if(!$process->save()) {
            throw new ParserException('Не удалось создать периодический процесс.');
        }
        
        return $process;
    }
    
    /**
     * Запуск полного цикла парсинга
     * @param bool $onlyCreate только создать процесс. 
     * По умолчанию 0 (нуль) процесс будет запущен сразу
     * после создания.
     */
    public function run($onlyCreate=false)
    {
        $process=$this->getActiveProcessByProcessHash();
        
        if($process) {
            $process->setParser($this->owner);
        }
        else {
            // очищаем таблицу процессов, если все процессы уже завершены
            $this->clear();
            
            // создаем новый процесс
            $process=$this->createProcess();
        }
        
        if(!$onlyCreate && $process->isReady()) {
            $process->run();
        }
        
        return $process;
    }
    
    /**
     * Запуск полного цикла периодического парсинга
     * @param int $startTime дата и время первого запуска процесса в формате Unix.
     * По умолчанию 0 (нуль) текущее дата и время.
     * @param int $duration интервал повторного запуска процесса в секундах.
     * По умолчанию 86400 (сутки)
     */
    public function runPeriodic($startTime=0, $duration=86400)
    {
        $process=$this->getPeriodicProcessByProcessHash();
        
        if($process) {
            $process->setParser($this->owner);
        }
        else {
            // очищаем таблицу процессов, если все процессы уже завершены
            $this->clear();
            
            // создаем новый периодический процесс
            $process=$this->createPeriodicProcess($startTime, $duration);
        }
        
        if($process->isReady()) {
            if($process->isDone()) {
                $process->resetProcess();
            }
            
            $process->run();
        }
        
        return $process;
    }
    
    /**
     * Создание нового процесса
     * 
     * @return int идентификатор созданного процесса
     */
    public function create()
    {
        $process=$this->getActiveProcess();
        
        if(!$process) {
            // очищаем таблицу процессов, если все процессы уже завершены
            $this->clear();
            
            $process=new Process; 
            $process->setParser($this->owner);
            $process->initConfigHash();
            
            if(!$process->save()) {
                throw new ParserException('Не удалось создать процесс.');
            }
        }
        
        return (int)$process->getId();
    }
    
    /**
     * Запуск следующей итерации процесса
     * 
     * @param int идентификатор процесса для которого запускается
     * следующая итерация.
     * 
     * @return int процент завершенности процесса
     */
    public function next($processId)
    {
        if(!empty($processId)) {
            if($process=Process::model()->resetScope()->findByPk($processId)) {
                $process->setParser($this->owner);
                
                $process->run();
                
                return $process->getPercent();
            }
        }
        
        throw new ParserException('Процесс не найден.');
    }
    
    /**
     * Очистка и нормализация таблиц парсера, если все процессы уже завершены
     * @param number $cacheTime интервал между запуском очистки таблиц парсера в секундах.
     * По умолчанию 86400 секунд (24 часа).
     * @param string $cacheId идентификатор кэша для хранения времени последней очистки кэша.
     * По умолчанию "common_ext_parser_parserIsCleaned".
     */
    public function clear($cacheTime=86400, $cacheId='common_ext_parser_parserIsCleaned')
    {
        if(!Y::cache()->get($cacheId)) {
            if(!Process::model()->resetScope()->byNotStatus(HStatus::DONE)->byIsPeriodic()->exists()) {
                HDb::query(
                    'DELETE FROM ' . HDb::qt(Process::model()->tableName())
                    . ';ALTER TABLE '.HDb::qt(Process::model()->tableName()).' AUTO_INCREMENT=1'
                    . ';ALTER TABLE '.HDb::qt(Group::model()->tableName()).' AUTO_INCREMENT=1'
                    . ';ALTER TABLE '.HDb::qt(Page::model()->tableName()).' AUTO_INCREMENT=1'
                    . ';ALTER TABLE '.HDb::qt(Content::model()->tableName()).' AUTO_INCREMENT=1'
                );
                
                Y::cache()->set($cacheId, date('d.m.Y H:i:s'), $cacheTime);
            }
        }
    }
}