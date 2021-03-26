<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->getComponent()->registerYMap(); ?>
<? $active=$arResult['ACTIVE_ITEM']; ?>
<div class="regions__changecity">
    <a href="javascript:;" class="regions__changecity-btn js-changecity-btn"><?
        if(!empty($active['NAME'])) echo $active['NAME'];
        else echo 'Выберите Ваш город';
    ?></a>
    <div class="regions__changecity-popup js-changecity-popup" style="display:none">
        <div class="regions__changecity-popup-title">Выберите Ваш город</div>
        <div class="regions__changecity-popup-list">
        <? foreach($arResult['REGIONS'] as $region=>$cities): ?>
            <div class="regions__changecity-popup-list-region">
                <div class="regions__changecity-popup-list-region-title js-changecity-region"><?= $region; ?></div>
                <div class="regions__changecity-popup-list-region-cities js-changecity-cities">
                    <ul>
                    <? foreach($cities as $city): ?>
                        <li 
                            data-code="<?=$city['ITEM']['CODE']?>"
                            data-address="<?=$city['ITEM']['ADDRESS']?>"
                            data-map="<?=$city['ITEM']['MAP']?>"
                            class="js-changecity-city<? if(!empty($active['CODE']) && ($active['CODE'] == $city['ITEM']['CODE'])) echo ' active'; ?>"
                        ><?
                            echo $city['NAME']; 
                        ?></li>
                    <? endforeach; ?>
                    </ul>
                </div>
            </div>
        <? endforeach; ?>
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
