<?php
/**
 * Модель Город
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

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use cdek\components\CdekApi;

class City extends \common\components\base\ActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'cdek_cities';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
				
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
		return $this->getScopes([
				
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return $this->getRelations([
				
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
            ['cdek_id, fullname, cityname, oblname, postcode, center', 'safe'],
            ['ym_point_x, ym_point_y, ym_bounds_lx, ym_bounds_ly, ym_bounds_ux, ym_bounds_uy', 'safe']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
            'cdek_id'=>'Идентификатор в системе СДЭК',
            'fullname'=>'Полное наименование',
            'cityname'=>'Город',
            'oblname'=>'Область',
            'postcode'=>'Индекс',
            'center'=>'Центр',
            'ym_point_x'=>'Координата X на яндекс-карте',
            'ym_point_y'=>'Координата Y на яндекс-карте',
            'ym_bounds_lx'=>'Координата X нижнего ограничения на яндекс-карте',
            'ym_bounds_ly'=>'Координата Y нижнего ограничения на яндекс-карте',
            'ym_bounds_ux'=>'Координата X верхнего ограничения на яндекс-карте',
            'ym_bounds_uy'=>'Координата Y верхнего ограничения на яндекс-карте'
		]);
	}

    /**
     * Обновление геокоординат
     */
    public function updateGeoCode()
    {
        // получение геокоординат
        if(Y::param('cdek.geocode')) {
            if(!$this->ym_point_x) {
                CdekApi::setGeodata($this);
            }
        }
    }
    
    public function search()
    {
        $criteria=new \CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('fullname',$this->fullname,true);
		$criteria->compare('cityname',$this->cityname,true);
        $criteria->compare('oblname',$this->oblname,true);
		$criteria->compare('postcode',$this->postcode,true);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
    }
}
