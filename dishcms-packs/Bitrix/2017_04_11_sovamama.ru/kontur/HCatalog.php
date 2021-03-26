<?php
/**
 * Класс-помощник для торгового каталога
 */
namespace Kontur;

use Kontur\Core\Iblock\Tools\Db;
use Kontur\Core\Main\Data\Cache;

class HCatalog 
{
	/**
	 * Получить предложения товара.
	 * @param integer $productId идентификатор товара.
	 * @return array
	 */
	public static function getOffers($productId)
	{
		$product = self::getProduct($productId);
		
		$cacheId='offers_' . $productId;
		return Cache::get(Cache::YEAR, $cacheId, "/{$cacheId}", function($productId, $iblockId) {
			if($offers = \CCatalogSKU::getOffersList($productId, $iblockId)) {
				return $offers[$productId];
			}
			return array();
		}, array($productId, $product['IBLOCK_ID']), array('iblock'=>$product['IBLOCK_ID']));
	}
	
	/**
	 * Получить данные о товаре.
	 * @param integer $id идентификатор товара.
	 * @return array
	 */
	public static function getProduct($id)
	{
		$cacheId="product_{$id}";
		return Cache::get(Cache::YEAR, $cacheId, "/{$cacheId}", function($id) {
			$product=Db::fetchAll(\CIBlockElement::GetByID($id));
			if(!empty($product)) {
				return $product[0];
			}
			return array();
		}, array($id), array('iblock'=>'byElement'));
	}
	
	/**
	 * Получить информацию о товаре.
	 * @param integer $offerId идентификатор предложения
	 */
	public static function getProductInfo($offerId)
	{
		$cacheId="productInfo_{$offerId}";
		return Cache::get(Cache::YEAR, $cacheId, "/{$cacheId}", '\CCatalogSku::GetProductInfo', array($offerId), array('iblock'=>'byElement'));
	}
	
	/**
	 * Получить идентификатор товара
	 * @param integer $offerId идентификатор предложения
	 */
	public static function getProductId($offerId)
	{
		$productId = false;
		
		if($productInfo = self::getProductInfo($offerId)) {
			$productId = $productInfo['ID'];
		}
		
		return $productId;
	}
	
	/**
	 * Получить все цены товара или цену товара, если передан $catalogGroupId.
	 * @param integer $id идентификатор товара или предложения.
	 * @param integer $catalogGroupId идентификатор типа цены.
	 * По умолчанию (FALSE) будут возвращены все типы цен.
	 * @return array
	 */
	public static function getPrices($id, $catalogGroupId=false)
	{
		$cacheId = "prices_{$id}";
		
		// получаем все типы цен, возможные для данного товара
		$prices = Cache::get(Cache::YEAR, $cacheId, "/{$cacheId}", function($id) {
			$prices = array();
			if($rs = \CPrice::GetListEx(array(), array('PRODUCT_ID'=>$id))) {
				$prices = Db::fetchAll($rs, 'CATALOG_GROUP_ID');
				$product = self::getProduct($id);
				foreach($prices as $catalogGroupId=>$data) {
					$prices[$catalogGroupId]['IBLOCK_ID'] = $product['IBLOCK_ID'];
				}
			}
			return $prices;
		}, array($id), array('iblock'=>'byList'));
			
		if($catalogGroupId) {
			return $prices[$catalogGroupId];
		}
			
		return $prices;
	}
	
	/**
	 * Получить коэффициент единицы измерения товара.
	 * @param integer $id идентификатор товара.
	 * @return float коэффициент единицы измерения товара.
	 */
	public static function getMeasureRatioByProductId($productId)
	{
		$product=self::getProduct($productId);
		
		$cacheId='measureRatio_' . $productId;
		return Cache::get(Cache::YEAR, $cacheId, "/{$cacheId}", function($productId) {
			$measureRatio = 1;			
			if($rsMeasureRatio = \CCatalogMeasureRatio::getList(array(), array("PRODUCT_ID"=>$productId), false, false, array())) {
				$measure = $rsMeasureRatio->Fetch();
				if(!empty($measure["RATIO"])) {
					$measureRatio = (float)$measure["RATIO"];
				}
			}
			return $measureRatio;
		}, array($productId), array('iblock'=>$product['IBLOCK_ID']));
	}	
}
?>