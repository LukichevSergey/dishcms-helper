<?php
namespace common\ext\iterator\behaviors;

use common\components\helpers\HRequest as R;
use common\components\helpers\HAjax;
use common\ext\iterator\models\Config;
use common\ext\iterator\models\Process;

/**
 * Поведение AJAX контроллера итератора для раздела админстрирования.
 *
 */
class IteratorCrudAjaxControllerBehavior extends \CBehavior
{
    /**
     * Action: Запуск процесса итератора
     *
     */
    public function actionRun($cid)
    {
        $ajax=HAjax::start();
        
        if($configHash=R::post(Config::CONFIG_HASH_VAR)) {
            $process=new Process();
            if($process->loadConfigByConfigHash($configHash)) {
                if($hash=R::post($process->getConfig()->getHashVar())) {
                    $process->setHash($hash);
                    $process->setParams(R::post($process->getConfig()->getParamsVar()));
                    if($process->next()) {
                        $ajax->data[$process->getConfig()->getParamsVar()]=$process->getParams();
                        $ajax->data['percent']=$process->getPercent();
                        $ajax->success=($ajax->data['percent'] > 0);
                    }
                }
                else {
                    if($process->create()) {
                        $ajax->data['hash']=$process->getHash();
                        $ajax->success=true;
                    }
                }
            }
            
            if($process->hasErrors()) {
                $ajax->success=false;
                $ajax->addErrors($process->getErrors());
            }
        }
        
        $ajax->end();
    }
    
    /**
     * Action: Создание нового процесса
     * 
     */
    public function actionCreate($cid)
    {
        $ajax=HAjax::start();
        
        if($configHash=R::post(Config::CONFIG_HASH_VAR)) {
            $process=new Process();
            if($process->loadConfigByConfigHash($configHash)) {
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
        
        $ajax->end();
    }
    
    /**
     * Action: Запуск итерации процесса
     * 
     */
    public function actionNext($cid)
    {
        $ajax=HAjax::start();
        
        if($configHash=R::post(Config::CONFIG_HASH_VAR)) {
            $process=new Process();
            if($process->loadConfigByConfigHash($configHash)) {
                if($hash=R::post($process->getConfig()->getHashVar())) {
                    $process->setHash($hash);
                    $process->setParams(R::post($process->getConfig()->getParamsVar()));
                    if($process->next()) {
                        $ajax->data[$process->getConfig()->getParamsVar()]=$process->getParams();
                        $ajax->data['percent']=$process->getPercent();
                        $ajax->success=($ajax->data['percent'] > 0);
                    }
                }
            }
            
            if($process->hasErrors()) {
                $ajax->success=false;
                $ajax->addErrors($process->getErrors());
            }
        }
        
        $ajax->end();
    }    
}