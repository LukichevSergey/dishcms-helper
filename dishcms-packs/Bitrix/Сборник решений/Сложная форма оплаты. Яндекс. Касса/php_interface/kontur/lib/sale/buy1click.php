<?
/**
 * Basket
 *
 * @link https://mrcappuccino.ru/blog/post/work-with-basket-bitrix-d7
 */
namespace Kontur\Core\Sale;

use Bitrix\Main\Localization\Loc,
    Kontur\Core\Sale\Basket,
    Kontur\Core\Main\Tools\Data;

\Bitrix\Main\Loader::includeModule('sale');

Loc::loadMessages(__FILE__);

class Buy1Click 
{
   /**
     * Оформление заказа в 1 клик. Для одного товара.
     *
     * Если пользователь авторизован, то заказ оформляется на данного пользователя,
     * иначе заказ будет оформлен на анонимного пользователя, переданные данные ($arUser) будут
     * добавлены как свойства товара.
	 *
     * @param int $id идентификатор товара.
     * @param integer $personTypeId идентификатор типа плательщика.
     * @param integer $priceTypeId идентификатор типа цены.
     * @param integer $quantity кол-во добавляемого товара. По умолчанию 1(один).
     * @param array $arOrderParams массив дополнительных параметров для заказа
     * см. $arFields https://dev.1c-bitrix.ru/api_help/sale/classes/csaleorder/csaleorder__add.5a463c02.php
     * @param array $arOrderProps массив дополнительных свойств заказа вида array(code=>value).
     * @param string(Y/N) $vatInclude НДС включён. По умолчанию "Y".     *
     * @param int $currencyId идентификатор валюты. Если задан, то произойдет конвертация цен 
     * в валюту $currencyId по текущему курсу.
     * @param string|NULL $comment комментарий к заказу. 
     * NULL - будет установлен заданный по умолчанию;
     * пустое значение или FALSE - комментарий добавлен не будет;
     * @param array $anonymous параметры анонимного пользователя, см. Buy1Click::getUserId()    
     * @see Торговый каталог/функции CatalogGetPriceTableEx()
     *
     * @return int id заказа.
     * @see CSaleOrder::Add()
     */
    public static function buyProduct($id, $personTypeId, $priceTypeId, $quantity=1, $arOrderParams=[], 
        $arOrderProps=[], $vatInclude="Y", $currencyId=false, $comment=null, $anonymous=array())
    {
        $orderId=static::createOrder(
            static::getUserId($anonymous), 
            $personTypeId, 
            static::getProductPrice($id, $priceTypeId, $vatInclude, $currencyId),
        	$arOrderParams, 
        	$arOrderProps,
            $comment
        );
        
        if($orderId) {
            \Add2BasketByProductID($id, $quantity);
            return $orderId;
        }
        
        return false;
    }
    
    /**
     * Оформить заказ из корзины
     * 
     * @param integer $personTypeId идентификатор типа плательщика
     * @param array $currency код валюты. По умолчанию "RUB".
     * @param array $arOrderParams массив дополнительных параметров для заказа
     * см. $arFields https://dev.1c-bitrix.ru/api_help/sale/classes/csaleorder/csaleorder__add.5a463c02.php
     * @param array $arOrderProps массив дополнительных свойств заказа.
     * @param string|NULL $comment комментарий к заказу. 
     * NULL - будет установлен заданный по умолчанию;
     * пустое значение или FALSE - комментарий добавлен не будет;
     * @param array $anonymous параметры анонимного пользователя, см. Buy1Click::getUserId()
     *
     * @return integer идентификатор заказа.
     */
    public static function buy($personTypeId, $currency='RUB', $arOrderParams=[], $arOrderProps=[], $comment=null, $anonymous=[])
    {
        $userId=static::getUserId($anonymous);        
        if($orderId=static::createOrder($userId, $personTypeId, static::getBasketPrice($currency), $arOrderParams, $arOrderProps, $comment)) {
            \CSaleBasket::OrderBasket($orderId);
            return $orderId;
        }
        
        return false;
    }
    
    /**
     * Создать заказ
     * 
     * @param integer $userId идентификатор пользователя
     * @param integer $personTypeId идентификатор типа плательщика
     * @param array $arPrice массив итоговой стоимости заказа вида 
     * array('PRICE' => <стоимость>, 'CURRENCY' => <код валюты, напр., "RUB">)
     *
     * @param array $arOrderParams массив дополнительных параметров для заказа
     * см. $arFields https://dev.1c-bitrix.ru/api_help/sale/classes/csaleorder/csaleorder__add.5a463c02.php
     * @param array $arOrderProps массив дополнительных свойств заказа.
     * @param string|NULL $comment комментарий к заказу. 
     * NULL - будет установлен Loc::getMessage('ORDER_COMMENTS');
     * пустое значение или FALSE - комментарий добавлен не будет;
     *
     * @return integer идентификатор заказа.
     */
    protected static function createOrder($userId, $personTypeId, $arPrice, $arOrderParams=[], $arOrderProps=[], $comment=null)
    {
        if($comment === null) {
            $comment = Loc::getMessage('ORDER_COMMENTS');
        }
        
        if($arPrice !== false) 
        {            
            $arOrderFields = array(
                'LID' => \SITE_ID,
                'USER_ID' => $userId,
                'PERSON_TYPE_ID' => $personTypeId,
                'PRICE' => $arPrice['PRICE'],
                'CURRENCY' => $arPrice['CURRENCY']            
            );
            if($comment) {
                $arOrderFields['COMMENTS'] = $comment;
            }
            
            if(empty($arOrderParams)) {
            	$arOrderParams=[];
            }
            $arOrderFields = array_merge($arOrderFields, $arOrderParams);
            
            if($orderId=\CSaleOrder::Add($arOrderFields)) {
            	Order::addOrderProperties($orderId, $arOrderProps);
            }

            return $orderId;
        }
        
        return false;
    }
    
    /**
     * @param boolean $returnBase (TRUE) возвращает цену корзины без учета скидок
     */
    protected static function getBasketPrice($currency, $returnBase=false)
    {
        if(Basket::getItemsCount()) {
            $basket=Basket::getBasket();
            if($returnBase) {
                $price=$basket->getBasePrice();
            }
            else {
                $price=$basket->getPrice();
            }
            
            return [
                'PRICE'=>$price,
                'CURRENCY'=>'RUB'
            ];
        }
        
        return false;
    }
    
    protected static function getProductPrice($id, $priceTypeId, $vatInclude="Y", $currencyId=false)
    {
        $arCurrencyParams=array();
        if($currencyId) {
            $arCurrencyParams['CURRENCY_ID']=$currencyId;
        }

        if($arPrice=\CatalogGetPriceTableEx($id, 0, [$priceTypeId], $vatInclude, $arCurrencyParams)) {
 			$price=$arPrice['MATRIX'][$priceTypeId][0]['PRICE'];
 			if($price != $arPrice['MATRIX'][$priceTypeId][0]['DISCOUNT_PRICE']) {
 				$price=$arPrice['MATRIX'][$priceTypeId][0]['DISCOUNT_PRICE'];
 			}
            
            return [
                'PRICE'=>$price,
                'CURRENCY'=>$arPrice['MATRIX'][$priceTypeId][0]['CURRENCY']
            ];
 		}
        
        return false;
    }
    
    /*
     * @param array $anonymous массив параметров анонимного пользователя,
     * на которого оформляются заказы неавторизованных пользователей.
     * Поля по умолчанию следующие:
     *   array(
     *       "LOGIN" => "buy1click",
     *       "NAME" => Loc::getMessage('ORDER_COMMENTS'),
     *       "EMAIL" => $ANONYMOUS["LOGIN"] . '@' . $_SERVER['SERVER_NAME']
     *       "PASSWORD" => uniqid()
     *   );
     *
     * @return integer идентификатор пользователя.
     */
    protected static function getUserId($anonymous=[])
    {
        global $USER;

        $userId=null;
        if($USER->IsAuthorized()) {
            $userId=IntVal($USER->GetId());
        }
        else {
            $login = Data::get($anonymous, 'LOGIN', 'buy1click');

            $rsUser=\CUser::GetByLogin($login);
            if($arUser = $rsUser->Fetch()) {
                $userId=$arUser['ID'];
            }
            else {
                $user = new \CUser;

                $password = Data::get($anonymous, 'PASSWORD', uniqid());
                $fields = [
                    'NAME' => Data::get($anonymous, 'NAME', Loc::getMessage('ORDER_COMMENTS')),
                    'EMAIL' => Data::get($anonymous, 'EMAIL', ($login.'@'.$_SERVER['SERVER_NAME'])),
                    'LOGIN' => $login,
                    'LID' => \SITE_ID,
                    'ACTIVE' => 'Y',
                    'PASSWORD' => $password,
                    'CONFIRM_PASSWORD' => $password
                ];

                $userId = $user->Add($fields);
            }
        }
        
        return $userId;
    }
}