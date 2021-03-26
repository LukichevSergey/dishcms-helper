<?php
namespace ecommerce\modules\exchange\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use common\components\helpers\HHash;

class ExcelImport
{
    const TMP_DIR='application.runtime.ecommerce.import';
    
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
            $configFile=\Yii::getPathOfAlias($configAlias) . '.php';
            if(strpos(preg_replace('#[/\\\\]+#', '/', $configFile), preg_replace('#[/\\\\]+#', '/', \Yii::getPathOfAlias('webroot'))) === 0) {
                if(is_file($configFile)) {
                    if($file=\CUploadedFile::getInstanceByName('filename')) {
                        $tmpUploadDir=\Yii::getPathOfAlias(self::TMP_DIR);
                        if(!is_dir($tmpUploadDir)) {
                            HFile::mkDir($tmpUploadDir, 0755, true);
                        }
                        
                        if(is_dir($tmpUploadDir)) {
                            $filename= "{$tmpUploadDir}/" . date('Y_m_d') . '.xlsx';
                            
                            // @todo если zip архив - распаковываем его
                            /* if(pathinfo($file->getName(), PATHINFO_EXTENSION) == 'zip') {
                                $zip=new \ZipArchive();
                                // $zip->close();
                            }
                            */
                            
                            $file->saveAs($filename);
                            
							$csvFilename=dirname($filename) . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.csv';
                            $spreadsheet=IOFactory::load($filename);
							$writer=IOFactory::createWriter($spreadsheet, 'Csv');
							$writer->setDelimiter(';');
                            $writer->save($csvFilename);
                            unlink($filename);
                            $filename=$csvFilename;

                            return [
								'filename'=>$filename, 
								'config'=>$configFile, 
								'limit'=>R::post('limit', 10),
								'start_position'=>(int)R::post('start_position', 0)
							];
                        }
                        else {
                            $iteratorProcess->addError('Не удалось создать временную директорию на сервере');
                        }
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
        $filename=$iteratorProcess->getDataParam('filename');
        
        if(!is_file($filename)) {
            $iteratorProcess->addError('Файл не найден');
            return -1;
        }        
        
        $configFile=$iteratorProcess->getDataParam('config');
        if(is_file($configFile) && ($hImportData=A::get(HFile::includeFile($configFile, []), 'data'))) {
            $iteration=$iteratorProcess->getParam('iteration', 0);
            $limit=$iteratorProcess->getDataParam('limit', 10);
			$startPosition=$iteratorProcess->getDataParam('start_position', 0);
            $headers=[];
            $iteratorProcess->setParam('stime', date('H:i:s'));
            try {
            	$csvReadIterator=HFile::readCsvIterator($filename, ';');
            	foreach($csvReadIterator as $row) {
            		$headers=$row;
            		break;
            	}
            	$iteratorProcess->setParam('stime_r1', date('H:i:s'));    
                $maxCol=count($headers);
                $maxRow=HFile::getCsvCountLines($filename);
                $beginRow=($iteration * $limit) + 2 + $startPosition;
                $endRow=(($iteration + 1) * $limit) + 2 + $startPosition;
                if($endRow > $maxRow) {
                    $endRow=$maxRow;
                }
                if(($beginRow <= $maxRow) && ($endRow <= $maxRow)) {
                	$n=1;
            		$data=[];
                	foreach(HFile::readCsvIterator($filename, ';') as $row) {
                		if($n > $endRow) break;
                		if($n >= $beginRow) $data[]=$row;
                		$n++;
                	}
					$iteratorProcess->setParam('stime_r2', date('H:i:s'));    
                    $info=[
                        'spreadsheet'=>null,
                        'max_columns'=>$maxCol,
                        'max_rows'=>$maxRow,
                        'begin_row'=>$beginRow,
                        'end_row'=>$endRow,
                        'iteration'=>$iteration,
                        'limit'=>$limit
                    ];
                    
                    call_user_func_array($hImportData, [$iteratorProcess, $data, $headers, $info]);
                    $iteratorProcess->setParam('etime', date('H:i:s'));
		            $percent=floor((($limit * ($iteration + 1) + $startPosition) * 100) / $maxRow); 
                    $iteratorProcess->setPercent($percent ?: 1);
                }
                else {
                    $iteratorProcess->setPercent(100);
                }                
            }
            catch(\Throwable $e) {
                $iteratorProcess->addError($e->getMessage());
                @unlink($filename);
				if(YII_DEBUG) {  throw $e; }
            }
            
            if($iteratorProcess->getPercent() >= 100) {
                $iteratorProcess->setPercent(100);
                @unlink($filename);
            }
            
            $iteratorProcess->setParam('iteration', $iteration + 1);
            
            return $iteratorProcess->getPercent();
        }
        
        $iteratorProcess->addError('Не найден файл конфигурации импорта данных');
        
        @unlink($filename);
        
        return -1;        
    }
}
