<?
/** @var \extend\points\controllers\PointController $this */
/** @var \CActiveDataProvider[\crud\models\ar\extend\points\models\Point] $dataProvider */
use common\components\helpers\HRequest as R;
use common\components\helpers\HHtml;

$this->widget('zii.widgets.CListView', array(
    'id'=>'pointAjaxListView',
    'dataProvider'=>$dataProvider,
    'itemView'=>'_point_listview_item',
    'enableHistory'=>true,
    'pagerCssClass'=>'pagination',
    'loadingCssClass'=>'loading-content',
    'emptyText' => 'Магазинов не найдено',
    'itemsTagName'=>'ul',
    'itemsCssClass'=>'map__results results',
    'sortableAttributes'=>false,
    'afterAjaxUpdate'=>'function(){window.extendPointsInit.refresh();}',
    'template'=>'{items}{pager}',
    'pager'=>[
        'class'=>'\common\components\pagers\MorePager',
        'moreOptions'=>[
            'id'=>'addressesmorebtn',
            'url'=>'/extend/points/point/index',
            'container'=>'.map__results.results',
            'label'=>'Загрузить еще <i class="fas fa-arrow-right"></i>',
            'htmlOptions'=>['class'=>'map__get-more btn'],
            'jsGetUrl'=>'return $.fn.yiiListView.getUrl("pointAjaxListView");',
            'checkPageCount'=>true,
            'onAfterUpdate'=>'window.extendPointsInit.refresh();'
        ]
    ]
));
?>
<script>
window.extendPointsInit=(function() {
	var _this={};
	
	_this.points={};
	_this.container=null;
	_this.currentCenter=null;
	_this.map=null;
	_this.yroute=null;	

	/**
	 * Доступные имена элементов:
     * "point_item" - основной контейнер точки продаж
     * "title" - наименование точки продаж
     * "detail-link" - ссылка на блок "подробнее" точки продаж
     * "distance" - значение дистанции до точки продаж
     * "create-route" - кнопка "проложить маршрут" 
	 */
	function n(name) { return '[data-item="' + name + '"]'; }
	function jq(name) { return _this.container + ' ' + n(name); }  
	function get(name) { return $(jq(name)); }
	function getAll(name) { return $(n(name)); }
	function getById(id, name) { return $(_this.container + ' [data-id="' + id + '"]' + ((typeof(name) == 'undefined') ? '' : (' ' + n(name)))); }

	_this.refresh=function(updateCurrentCenter) {
		if((typeof(updateCurrentCenter) != 'undefined') && updateCurrentCenter) {
			var center=_this.map.getCenter();
			_this.currentCenter=center;
			_this.setCurrentCenter(center);
		}
		_this.addPoints(true);
		_this.updateDistances();
	};
	
	_this.getCoordinates=function(id) {
		var point=getById(id);
		if(point.length && point.data('point').length) {
			return eval('(function(){ var c=' + point.data('point') + ';return [c.lat,c.lon];})()');
		}
		return false;
	}

	_this.addPoints=function(reload) {
		if((typeof(reload) != 'undefined') && reload) _this.points={};
		var id; get('point_item').each(function() {
			id=$(this).data('id');
			if(typeof _this.points[id] == 'undefined') {
				_this.points[id]={
					id: id,
					coordinates: _this.getCoordinates(id),
					distance: ''
				}
			}
		});
	};

	_this.setCurrentCenter=function(center) {
		//_this.currentCenter=center;
		_this.map.setCenter(center);
	};

	_this._activePoint=null;
	_this._getNextActivePoint=function(cycle) {
		if($.isEmptyObject(_this.points)) {
			_this._activePoint=null;
		}
		else {
    		cycle=((typeof(cycle) != 'undefined') && cycle);
    		_this._activePointFound=false;
    		for(var id in _this.points) {
    			if(!_this._activePoint) {
    				_this._activePoint=_this.points[id];
    				break;
    			}
    			else if(_this._activePoint.id == id) {
    				_this._activePoint=null;
    				_this._activePointFound=true;
    			}
    			else if(_this._activePointFound) {
    				_this._activePoint=_this.points[id];
    				break;
    			}
    		}
    
    		if(cycle) {
    			_this._activePoint=_this._getNextActivePoint();
    		}
		} 
		
		return _this._activePoint;
	};

	_this.getPointByCoordinates=function(coordinates) {
		for(var id in _this.points) {
			if((_this.points[id].coordinates[0] == coordinates[0]) && (_this.points[id].coordinates[1] == coordinates[1])) {
				return _this.points[id];
			}
		}
		return null;
	};

	_this.updateDistances=function() {
		var point=_this._getNextActivePoint();
		if(point) {
			if(_this.yroute === null) {
				_this.yroute=new ymaps.multiRouter.MultiRoute({
					referencePoints: [_this.currentCenter, point.coordinates],
					params: {results: 1}
				}, { 
					boundsAutoApply:true 
				});
				
				_this.yroute.events.add('update', function(r) {
					var referencePoints=r.originalEvent.target.getActiveRoute().model.multiRoute.getReferencePoints();
					if(point=_this.getPointByCoordinates(referencePoints[1])) {
						point.distance=r.originalEvent.target.getActiveRoute().properties.get('distance').text;
						getById(point.id, 'distance').text(point.distance);
					}
					_this.updateDistances();
				});
			}
			else {
				_this.yroute.model.setReferencePoints([_this.currentCenter, point.coordinates]);
			}
		}
	};

	_this.getItemByEvent=function(e) {
		return $(e.target).parents(n('point_item') + ':first');
	};

	_this._lastPointMultiRoute=null;
	_this.onClickCreateRoute=function(e) {
		if(_this._lastPointMultiRoute) {
			_this.map.geoObjects.remove(_this._lastPointMultiRoute);
		}
		var item=_this.getItemByEvent(e);
		var coordinates=_this.getCoordinates(item.data('id'));
		var multiRoute=new ymaps.multiRouter.MultiRoute({
			referencePoints: [_this.currentCenter, coordinates],
			params: {results: 1}
		}, { 
			boundsAutoApply:true 
		});
		_this.map.geoObjects.add(multiRoute);
		_this._lastPointMultiRoute=multiRoute;
	};
	
	_this.init=function(map, container) {
		_this.map=map;
		_this.container=container;
		_this.refresh(true);

		$(document).on('click', jq('create-route'), _this.onClickCreateRoute);

		$(document).on('click', jq('detail-link'), function(e) {
			$('html, body').animate({scrollTop: getAll('point_info').offset().top}, 1000);
		});
		
		$(document).on('click', jq('title'), function(e) {
			var item=_this.getItemByEvent(e), id=item.data('id');

			getAll('detail-link').hide();
			_this.setCurrentCenter(_this.getCoordinates(id));
			
			$.post('/extend/points/point/info', {id: id}, function(r) {
				if(r.success) {
					if(!r.data.info) r.data.info='Информацию уточняйте по телефону поддержки покупателей <?= HHtml::phoneLink(D::cms('phone')); ?>';

					getAll('point_info-content').html(r.data.info);
					getAll('point_info-photos').html('');

					r.data.photos.forEach(function(photo) {
						var src='/images/uploader/extend_points/' + photo;
						getAll('point_info-photos').append(
							'<a href="'+src+'" data-fancybox="images" data-caption="'+r.data.title+'">'
							+ '<div style="background:url(' + src + ') no-repeat;width:350px;height:350px;background-size:cover;margin-bottom:3px;"></div>'
							+ '</a>'
						); 
					});

					getById(id, 'detail-link').show();
					getAll('point_info').show();
				}
				else {
					getAll('point_info').hide();
				}
			}, 'json');
		});
	};

	return _this;
})();
</script>