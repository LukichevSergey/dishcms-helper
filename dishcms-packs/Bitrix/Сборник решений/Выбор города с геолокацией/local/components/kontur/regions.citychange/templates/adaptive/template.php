<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><? $this->getComponent()->registerYMap(); ?>
<? $active=$arResult['ACTIVE_ITEM']; ?>
<div class="regions__changecity">
    <a href="javascript:;" class="regions__changecity-btn js-changecity-btn"><?
        if(!empty($active['NAME'])) echo $active['NAME'];
        else echo 'Выберите Ваш город';
    ?></a>
    <div class="regions__changecity-popup js-changecity-popup" style="display:none">
        <div class="regions__changecity-popup-title">Выберите Ваш город</div>
        <div class="regions__changecity-popup-lists">
<? 
$regionHtml='';
$cityHtml='';
$regionIdx=1;
foreach($arResult['REGIONS'] as $region=>$cities) {
 $regionHtml.='<li class="regions__changecity-popup-list-region-title js-changecity-region" data-item="'.$regionIdx.'">'.$region.'</li>';
 $cityHtml.='<li class="regions__changecity-popup-list-region-cities js-changecity-cities" data-item="'.$regionIdx.'"><ul>';
 foreach($cities as $city) {
   $cityHtml.='<li class="js-changecity-city'.((!empty($active['CODE']) && ($active['CODE'] == $city['ITEM']['CODE'])) ? ' active' : '').'"'
     . ' data-code="'.$city['ITEM']['CODE'].'"'
     . ' data-address="'.$city['ITEM']['ADDRESS'].'"'
     . ' data-map="'.$city['ITEM']['MAP'].'">'.$city['NAME'].'</li>';
 }
 $cityHtml.='</ul></li>';
 $regionIdx++;
}
?>
<ul class="regions__changecity-popup-list"><?= $regionHtml; ?></ul>
<ul class="regions__changecity-popup-list"><?= $cityHtml; ?></ul>

        </div>
        <div class="regions__changecity-popup-info js-changecity-info">
            <div id="konturchangecitymap" class="regions__changecity-popup-info-map"></div>
            <div class="regions__changecity-popup-info-address js-changecity-info-address"></div>
        </div>
    </div>
</div>
<script>;BX.ready(function(){
window.konturRegionsChangeCityIntervalId=setInterval(function(){
    if(typeof window.konturRegionsChangeCity != 'undefined') {
        clearInterval(window.konturRegionsChangeCityIntervalId);
        window.konturRegionsChangeCity.init({
            yid: 'konturchangecitymap',
            yzoom: 16,
            ycenter: [55.029030, 82.926474],
            cookie: '<?=$arParams['COOKIE_KEY']?>',
            geolocation: <?=($arParams['DISABLE_YANDEX_GEOIP']=='Y')?'false':'true'?>
        });
    }
},200);});</script>
