<?php
/** @var \extend\points\controllers\PointController $this */
use common\components\helpers\HHash;
use extend\modules\points\components\helpers\HPoint;
use crud\models\ar\extend\points\models\Point;

$mapId=HHash::u('pointsmap');
?>
<section class="map">
	<?php $this->widget('\common\ext\ymap\widgets\YMap', [
	    'apikey'=>HPoint::settings()->apikey,
	    'options'=>[
	        'id'=>$mapId,
            'x'=>55.028888,
            'y'=>82.926484,
    	    'points'=>Point::model()->getPoints(),
            'controls'=>['zoomControl', 'geolocationControl', 'fullscreenControl'],
	        'placemarkOptions'=>HPoint::getPlacemarkOptions(),
            'onAfterInit'=>'js:function(map){let wsf=window.extendPointsWidgetsSearchForm;wsf.setOptions({'
	           .'gotoGeoLocationOnAfterInit:true,'
	           .'searchListView:"pointAjaxListView",'
	           .'onAfterSearchOutput:function(){$("js-map__addresses-contact").hide();},'
               .'onAfterClickGoto:function(){window.extendPointsInit.refresh(true);}'
               .'});wsf.onAfterInitMap(map);window.extendPointsInit.init(map,".js-map__addresses");}'
        ], 
	    'htmlOptions'=>[
	        'class'=>'map__init', 
	        'style'=>'width:100%;height:400px;'	        
	    ]
	]);?>
	
	<div class="map__container container">
		<div class="map__row row">
			<div class="map__col col-lg-8">
				<?php $this->widget('\extend\modules\points\widgets\SearchForm', [
				    'map'=>$mapId,
				    'tagOptions'=>['class'=>'map__header'],
				    'formOptions'=>['class'=>'map__search search'],
				    'htmlOptions'=>[
				        'class'=>'search__input input',
				        'placeholder'=>'Индекс, улица или город'
				    ],
				    'submitLabel'=>'Найти',
				    'submitOptions'=>['class'=>'search__submit'],
				    'gotoLabel'=>'Определить GPS координаты',
				    'gotoOptions'=>[
				        'class'=>'map__get-gps text link link_br_dashed'
				    ]
				]); ?>
				
				<div class="map__addresses js-map__addresses">
					<?php $this->renderPartial('_point_listview', compact('dataProvider')); ?>
				</div>
			</div>
		</div>
	</div>
</section>
<div class="main__container container">
	<section class="contacts" style="display:none" data-item="point_info">
		<div class="contacts__row row">
			<div class="contacts__content-col col-lg-8">
				<h1>Контакты</h1>
				<div class="map__addresses-contact-body" data-item="point_info-content"></div>				
			</div>
			<div class="contacts__image-col col-lg-4">
				<div class="contacts__image-wrap" data-item="point_info-photos"></div>
			</div>
		</div>
	</section>
</div>