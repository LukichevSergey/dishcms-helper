<?php
namespace common\ext\parser\behaviors;

use common\components\helpers\HRequest as R;
use common\components\helpers\HAjax;
use common\ext\parser\models\Config as ParserConfig;
use common\ext\iterator\models\Config as IteratorConfig;
use common\ext\iterator\models\Process as IteratorProcess;
use crud\models\ar\common\ext\parser\models\Parser;

class ParserCrudAjaxControllerBehavior extends \CBehavior
{
    /**
     * Action: Запуск процесса парсинга
     * @param string $cid идентификатор CRUD конфигурации
     */
    public function actionRun($cid)
    {
        $ajax=HAjax::start();
        
        if($parserConfigHash=R::post(ParserConfig::CONFIG_HASH_VAR)) {
            if($parserConfig=ParserConfig::loadByHash($parserConfigHash)) {
                if($iteratorConfig=$parserConfig->getIteratorConfig()) {
                    $process=new IteratorProcess();
                    $process->setConfig($iteratorConfig);
                    if($hash=R::post($process->getConfig()->getHashVar())) {
                        $process->setHash($hash);
                        $process->setDataParam(ParserConfig::CONFIG_HASH_VAR, $parserConfigHash);
                        if($process->next()) {
                            $ajax->data['percent']=$process->getPercent();
                            $ajax->success=true;
                        }
                    }
                    else {
                        $process->setDataParam(ParserConfig::CONFIG_HASH_VAR, $parserConfigHash);
                        if($process->create()) {
                            $ajax->data['hash']=$process->getHash();
                            $ajax->success=true;
                        }
                    }
                    
                    if($process->hasErrors()) {
                        $ajax->success=false;
                        $ajax->addErrors($process->getErrors());
                    }
                }
            }
        }        
        
        $ajax->end();
    }
    
    /**
     * Action: Получить процент завершенности процесса, запущенного из консоли. 
     */
    public function actionGetCommandProcessPercent()
    {
        $ajax=HAjax::start();
        
        $ajax->success=true;
        $ajax->data['percent']=0;
        
        if($parserConfigHash=R::post(ParserConfig::CONFIG_HASH_VAR)) {
            if($parserConfig=ParserConfig::loadByHash($parserConfigHash)) {
                $parser=new Parser;
                $parser->setConfig($parserConfig);
                
                if((bool)R::post('is_periodic')) {
                    $process=$parser->getActivePeriodicProcessByProcessHash();
                }
                else {
                    $process=$parser->getActiveProcessByProcessHash();
                }
                
                if($process) {
                    $ajax->data['percent']=$process->getPercent();
                }
                else {
                    $ajax->data['percent']=100;
                }
            }
        }
        
        $ajax->end();
    }
}