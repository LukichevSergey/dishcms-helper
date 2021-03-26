<? $this->renderPartial('cdek.modules.admin.views.default._cdek_city_import_form', ['model'=>$cdekCityImportFormModel]); ?>

<h1>Города СДЭК</h1>
<?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'cdek-city-grid',
    'itemsCssClass'=>'table table-striped table-bordered table-hover',
    'pagerCssClass'=>'pagination',
    'dataProvider'=>$cityDataProvider,
    'filter'=>$cityDataProvider->model,
    'columns'=>[
        'id',
        'cdek_id',
        'fullname',
        'cityname',
        'oblname',
        'postcode',
        'center',
    ]
]); 
?>
