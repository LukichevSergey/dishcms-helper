<?php

class CityHelper
{
	public static $currentCity = null;

	/**
	 * Инициализация
	 * @use https://sypexgeo.net/ru/download/
	 */
	public static function init($sxGeoPath, $sxGeoData, $default='Новосибирск')
	{
		if(isset($_GET['city']) && ($city=City::model()->findByPk((int)$_GET['city']))) {
			\Yii::app()->user->setState('city_id', $city->id);
		}
		elseif(!\Yii::app()->user->hasState('city_id')) {
			include_once($sxGeoPath);
			$sxGeo=new SxGeo($sxGeoData);
			$geoIpData=$sxGeo->get($_SERVER['REMOTE_ADDR']);
			unset($sxGeo);
			if($city=City::model()->findByAttributes(['title'=>$geoIpData['city']['name_ru']])) {
				\Yii::app()->user->setState('city_id', $city->id);
			}
			elseif($city=City::model()->findByAttributes(['title'=>$default])) {
				\Yii::app()->user->setState('city_id', $city->id);
			}
		}
	}

    /**
     * @return array|mixed|null|City
     */
    public static function getCurrentCityModel()
    {
        if (static::$currentCity === null) {
            static::$currentCity = City::model()->findByPk(Yii::app()->user->getState('city_id', 1));
        }

        return static::$currentCity;
    }

    /**
     * @return Country
     */
    public static function getCurrentCounty()
    {
        $model = self::getCurrentCityModel();

        return $model->country;
    }

    public static function getCurrentCityAttribute($attribute)
    {
        $model = self::getCurrentCityModel();

        return $model->$attribute;
    }

    /**
     * @return int
     */
    public static function getCurrentCityID()
    {
        $city = static::getCurrentCityModel();

        return $city->id;
    }

    /**
     * @return City[]
     */
    public static function getCityList()
    {
        return City::model()->findAll(['order' => 'title']);
    }

    public static function getCityListData()
    {
        return CHtml::listData(static::getCityList(), 'id', 'title');
    }

}

