<?php
/**
 * Модель формы импорта городов СДЭК
 * 
 * @property id pk
 * @property cdek_id integer
 * @property fullname string
 * @property cityname string
 * @property oblname string
 * @property postcode string
 * @property center boolean
 */
namespace cdek\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

class CityImportForm extends \common\components\base\FormModel
{
    public $filename;
    public $skip_exists=1;
    
    public function rules()
    {
        return $this->getRules([
            ['filename', 'file', 'types'=>'csv']
        ]);
    }
    
    public function attributeLabels()
    {
        return $this->getAttributeLabels([
            'filename'=>'Файл импорта (CSV)',
            'skip_exists'=>'Пропустить существующие'
        ]);
    }
    
    public function import()
    {
        $importFile=\CUploadedFile::getInstance($this, 'filename');
        if($importFile instanceof \CUploadedFile) {
            if (($handle=fopen($importFile->getTempName(), "r")) !== false) {
                while (($rowData=fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                    if(!is_numeric($rowData[0])) {
                        continue;
                    }
                
                    if(!($city=City::model()->find('cdek_id=:cdekId', [':cdekId'=>$rowData[0]]))) {
                        $city=new City;
                    }
                    elseif((int)$this->skip_exists) {
                        continue;
                    }
                    
                    $city->cdek_id=$rowData[0];
                    $city->fullname=$rowData[1];
                    $city->cityname=$rowData[2];
                    $city->oblname=$rowData[3];
                    // UPDATE `cdek_cities` SET `postcode`=SUBSTRING(`postcode`, 1, LOCATE(',', `postcode`)-1) WHERE LOCATE(',', `postcode`)>0
                    $city->postcode=(strpos($rowData[7], ',') !== false) ? preg_replace('/^([^,]+),.*$/', '$1', $rowData[7]): '';
                    $city->center=(strlen(trim($rowData[4])) > 0) ? 1 : 0;
                    
                    if(!$city->save()) {
                        $this->addError('filename', '#'.$row . ': ' . implode(', ', $city->getErrors()));
                    }
                }
                fclose($handle);
            }
            else {
                $this->addError('filename', 'Error loading file "'.pathinfo($importFile->getName(),PATHINFO_BASENAME).'": '.$e->getMessage());
            }
        }

    }
}
