<?php
// CRON TASK
// */10 * * * * php -f /path/to/protected/yiic.php parse run --config=application.config.parsers.myparserconfig --delay=1800

namespace common\ext\parser\console;

use common\ext\parser\components\helpers\HStatus;
use common\ext\parser\components\helpers\HParser;
use common\ext\parser\components\exceptions\ParseCommandException;
use common\ext\parser\models\Config;
use crud\models\ar\common\ext\parser\models\Parser;

class ParseCommand extends \CConsoleCommand
{
    /**
     * Путь к файлу конфигурации процесса
     * @var string
     */
    private $config;
    
    /**
     * Активный процесс парсинга
     * @var \crud\models\ar\common\ext\parser\models\Process
     */
    private $process;
    
    /**
     * Action: запуск парсинга
     * @param string $config путь к файлу конфигурации
     * @param bool $onlyCreate только создать процесс, но не запускать его. 
     * Будет проигнорирован для периодических процессов.
     * @param int|string $startTime дата и время первого запуска процесса в формате Unix, либо 
     * в как строка в формате "ГГГГ-ММ-ДД ЧЧ:ММ:СС".
     * @param int $duration интервал повторного запуска процесса в секундах
     * @param int $showErrors показывать только ошибки. По умолчанию 0 (нуль) не показывать.
     * @param int $showMessages показывать сообщения. По умолчанию 0 (нуль) не показывать.
     * Если передано 1 (включить), то ошибки также будут отображены.
     */
    public function actionRun($config, $onlyCreate=0, $startTime=0, $duration=0, $showErrors=0, $showMessages=0)
    {
        try {
            $showErrors=(int)$showErrors;
            $showMessages=(int)$showMessages;
            
            if(preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $startTime)) {
                $startTime=date_create_from_format('Y-m-d H:i:s', $startTime)->format('U');
            }
            $startTime=(int)$startTime;
            $duration=(int)$duration;
            $isPeriodicProcess=($startTime && $duration);
            
            $this->config=$config;
            if($config=Config::load($this->config)) {
                $parser=new Parser;
                $parser->setConfig($config);
                
                // проверка на уже запущенный процесс парсинга
                if($isPeriodicProcess) {
                    if($process=$parser->getActiveProcessByProcessHash()) {
                        throw new ParseCommandException('Уже запущен простой процесс.');
                    }
                    elseif($process=$parser->getPeriodicProcessByProcessHash()) {
                        if(!$process->isReady()) {
                            throw new ParseCommandException('Процесс будет запущен ' . date('d.m.Y в H:i:s', $process->getReadyTime()?:time()) . '.');
                        }
                        elseif(!$process->pid || !HParser::isPHPRunned($process->pid)) {
                            $process->pid=(int)getmypid();
                            $process->update(['pid']);
                        }
                        else {
                            throw new ParseCommandException('Процесс уже запущен.');
                        }
                    }
                }
                elseif($process=$parser->getActiveProcessByProcessHash()) {
                    if(!$process->pid || !HParser::isPHPRunned($process->pid)) {
                        $process->pid=(int)getmypid();
                        $process->update(['pid']);
                    }
                    else {
                        throw new ParseCommandException('Процесс уже запущен.');
                    }
                }
                
                if($onlyCreate && !$isPeriodicProcess) {
                    $process=$parser->run(true);
                    if($process->pid) {
                        $process->pid=0;
                        $process->update(['pid']);
                    }
                }
                else {
                    set_time_limit(0);
                    
                    if($showMessages) echo "Процесс запущен! Выполнено: 0%";
                    
                    if($isPeriodicProcess) {
                        $process=$parser->runPeriodic($startTime, $duration);
                    }
                    elseif($process) {
                        $process=$parser->run();
                    }
                    
                    if($process) {
                        if($showMessages) echo "\rПроцесс запущен! Выполнено: " . $process->getPercent() . "%       ";
                        while(!$process->isDone()) {
                            $process->run();
                            
                            if($showMessages) echo "\rПроцесс запущен! Выполнено: " . $process->getPercent() . "%        ";
                        }
                    }
                }
            }
        }
        catch(ParseCommandException $e) {
            if($showMessages) echo $e->getMessage() . "\n";
        }
        catch(\Exception $e) {
            if($showMessages || $showErrors) echo "\n\nОшибка: {$e->getMessage()}\n\n";
            return 1;
        }
        
        return 0;
    }
}
