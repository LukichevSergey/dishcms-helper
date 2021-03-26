<?php
namespace ecommerce\modules\exchange\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use common\components\helpers\HHash;

class ExcelExport
{
    const TMP_DIR='application.runtime.ecommerce.export';
 
    /**
     * Получение дополнительного ключа шифрования
     * @return string
     */
    public static function secure()
    {
        return '&S"$SHBt!H(sZn8#';
    }
    
    /**
     * Создание процесса импорта
     * @param \common\ext\iterator\models\Process $iteratorProcess
     * @return []
     */
    public static function create($iteratorProcess)
    {
        if($config=R::post('config')) {
            $configAlias=HHash::srDecrypt($config, md5($iteratorProcess->getConfig()->getPath() . '.' . $iteratorProcess->getConfig()->getId()));
            $configFile=HFile::normalizePath(\Yii::getPathOfAlias($configAlias) . '.php');
            $webroot=HFile::normalizePath(\Yii::getPathOfAlias('webroot'));
            if(strpos($configFile, $webroot) === 0) {
                if(is_file($configFile)) {
                    $tmpDir=\Yii::getPathOfAlias(self::TMP_DIR);
                    if(!is_dir($tmpDir)) {
                        HFile::mkDir($tmpDir, 0755, true);
                    }
                    
                    if(is_dir($tmpDir)) {
                        $filename="{$tmpDir}/" . date('Y_m_d') . '.xlsx';
                        
                        if(is_file($filename)) {
                            @unlink($filename);
                        }
                        
                        return [
                            'filename'=>$filename, 
                            'config'=>$configFile, 
                            'limit'=>R::post('limit', 100),
                            'zip'=>(int)R::post('zip', 0)
                        ];
                    }
                    else {
                        $iteratorProcess->addError('Не удалось создать временную директорию на сервере');
                    }
                }
            }
        }
        
        $iteratorProcess->addError('Некорректный запрос');
        
        R::e400();
    }
    
    /**
     * Итерация импорта
     * @param \common\ext\iterator\models\Process $iteratorProcess
     * @return int процент завершенности
     */
    public static function next($iteratorProcess)
    {
        if($filename=$iteratorProcess->getDataParam('filename')) {
            $iteration=$iteratorProcess->getParam('iteration', 0);
            $limit=$iteratorProcess->getDataParam('limit', 10);
            
            $configFile=$iteratorProcess->getDataParam('config');
            if(is_file($configFile) && ($hExportData=A::get(HFile::includeFile($configFile, []), 'data'))) {
                $percent=-1;
                
                try {
                    $info=[
                        'filename'=>$filename,
                        'iteration'=>$iteration,
                        'limit'=>$limit
                    ];
                    
                    $percent=call_user_func_array($hExportData, [$iteratorProcess, $info]);
                }
                catch(\Throwable $e) {
                    $iteratorProcess->addError($e->getMessage());
                }
                
                $iteratorProcess->setPercent($percent);
                $iteratorProcess->setParam('iteration', $iteration + 1);
                
                if(($percent >= 100) && is_file($filename)) {
                    $uploadDir=\Yii::getPathOfAlias('webroot.upload');
                    if(!is_dir($uploadDir)) {
                        HFile::mkDir($uploadDir, 0755, true);
                    }
                    
                    if(is_dir($uploadDir)) {
                        copy($filename, $uploadDir . '/export_' . basename($filename));
                        $iteratorProcess->setParam('download', basename($filename));
                    }
                    else {
                        $iteratorProcess->addError('Не удалось сохранить файл экспорта на сервере для скачивания');
                    }
                    
                    @unlink($filename);
                }
                
                return $iteratorProcess->getPercent();
            }
            else {
                $iteratorProcess->addError('Не найден файл конфигурации импорта данных');
            }
        }
        else {
            $iteratorProcess->addError('Некорректный запрос');
        }
        
        return -1;
    }
}