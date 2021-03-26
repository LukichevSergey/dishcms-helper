<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Service\GeoIp;

if(!empty($_COOKIE['filialcity']) && !empty($_COOKIE['filialcitypoint'])) {
    $_SESSION["CITY"] = $_COOKIE['filialcity'];
    $_SESSION["CITY_POINT"] = $_COOKIE['filialcitypoint'];
    $point = explode(',', $_COOKIE['filialcitypoint']);
    $_SESSION['CITY_POINT_LATITUDE'] = $point[0];
    $_SESSION['CITY_POINT_LONGITUDE'] = $point[1];
}
else {
    $ipAddress = GeoIp\Manager::getRealIp();
    GeoIp\Manager::useCookieToStoreInfo(true);
    $geoData = GeoIp\Manager::getDataResult($ipAddress, "ru")->getGeoData();
    if(empty($geoData) || !$geoData->cityName) {
        $geoData=new stdClass();
        $geoData->cityName = 'Москва';
        $geoData->latitude = '55.725047';
        $geoData->longitude = '37.646866';
    }
    $_SESSION["CITY"] = $geoData->cityName;
    $_SESSION["CITY_POINT"] = $geoData->latitude . ',' . $geoData->longitude;
    $_SESSION['CITY_POINT_LATITUDE'] = $geoData->latitude;
    $_SESSION['CITY_POINT_LONGITUDE'] = $geoData->longitude;
}

$arResult['CITIES'] = [];
if(!empty($arResult['ITEMS'])) {
    foreach($arResult['ITEMS'] as $arItem) {
        if(!empty($arItem['PROPERTIES']['MAPS']['VALUE']) && !empty($arItem['PROPERTIES']['CITY']['VALUE'])) {
            $arResult['CITIES'][$arItem['PROPERTIES']['CITY']['VALUE']]=[
                'NAME' => $arItem['PROPERTIES']['CITY']['VALUE'],
                'POINT' => $arItem['PROPERTIES']['MAPS']['VALUE'],
                'SELECTED' => ($arItem['PROPERTIES']['CITY']['VALUE'] == $_SESSION["CITY"]) ? 'Y' : 'N'
            ];
        }
    }
    usort($arResult['CITIES'], function($a, $b){
        return strcmp($a['NAME'], $b['NAME']);
    });
}
?>

