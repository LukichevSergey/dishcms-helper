<?
/**
 * Купить в 1 клик
 */

if (! function_exists("KonturSaleBuyOneClick") )
{
	if (!CModule::IncludeModule("sale"))
	{
	    ShowError("SALE_MODULE_NOT_INSTALL");
    	return;
	}

    /**
     * Оформление заказа в 1 клик.
     *
     * Если пользователь авторизован, то заказ оформляется на данного пользователя,
     * иначе заказ будет оформлен на анонимного пользователя, переданные данные ($arUser) будут
     * добавлены как свойства товара.
	 *
     * @param int $ID id элемента товара
     *
     * @param array $arOrderParams массив дополнительных свойств заказа в формате array(PROPERTY_CODE=>VALUE)
     * Обязатльеный параметр для добавления заказа  PERSON_TYPE_ID - тип плательщика, к которому принадлежит
     * посетитель, сделавший заказ (заказчик);
     * Заполняется автоматически, но могут быть перезаписаны переданными параметрами:
     * LID, USER_ID, PRICE, CURRENCY, COMMENTS
     * @see CSaleOrder::Add()
     *
     * @param array $arOrderProps массив дополнительных свойств заказа вида array(code=>value).
     *
     * @param string(Y/N) НДС включён. По умолчанию "Y".
     * @see Торговый каталог/функции CatalogGetPriceTableEx()
     *
     * @param int $CURRENCY_ID id валюты. Если задан, то произойдет конвертация цен в валюту CURRENCY_ID по текущему курсу.
     * @see Торговый каталог/функции CatalogGetPriceTableEx()
     *
     * @param array $ANONYMOUS массив параметров анонимного пользователя,
     * на которого оформляются заказы неавторизованных пользователей.
     * Доступы следующие поля
     *   array(
     *       "LOGIN" => "buy1click",
     *       "NAME" => "Купить в 1 клик",
     *       "EMAIL" => $ANONYMOUS["LOGIN"] . '@' . $_SERVER['SERVER_NAME']
     *       "PASSWORD" => uniqid()
     *   );
     *
     * @return int id заказа.
     * @see CSaleOrder::Add()
     */
    function KonturSaleBuyOneClick($ID, $PRICE_TYPE_ID, $arOrderParams, $arOrderProps=array(), $VAT_INCLUDE="Y", $CURRENCY_ID=false, $ANONYMOUS=array())
    {
        global $USER;

        $USER_ID=null;
        if($USER->IsAuthorized()) {
            $USER_ID=IntVal($USER->GetId());
        }
        else {
            $login = empty($ANONYMOUS['LOGIN']) ? 'buy1click' : $ANONYMOUS['LOGIN'];

            $rsUser=CUser::GetByLogin($login);
            if($arUser = $rsUser->Fetch()) {
                $USER_ID=$arUser['ID'];
            }
            else {
                $user = new CUser;

                $password = empty($ANONYMOUS['PASSWORD']) ? uniqid() : $ANONYMOUS['PASSWORD'];
                $arFields = Array(
                    'NAME' => (empty($ANONYMOUS['NAME']) ? 'Купить в 1 клик' : $ANONYMOUS['NAME']),
                    'EMAIL' => (empty($ANONYMOUS['EMAIL']) ? ($login.'@'.$_SERVER['SERVER_NAME']) : $ANONYMOUS['EMAIL']),
                    'LOGIN' => $login,
                    'LID' => SITE_ID,
                    'ACTIVE' => 'Y',
                    'PASSWORD' => $password,
                    'CONFIRM_PASSWORD' => $password
                );

                $USER_ID = $user->Add($arFields);
            }
        }

        $arCurrencyParams=array();
        if($CURRENCY_ID) $arCurrencyParams['CURRENCY_ID']=$CURRENCY_ID;

		$iPrice=0;
		$sCurrency='RUB';
        $arPrice=CatalogGetPriceTableEx($ID, 0, array($PRICE_TYPE_ID), $VAT_INCLUDE, $arCurrencyParams);
        if(!empty($arPrice)) {
 			$iPrice=$arPrice['MATRIX'][$PRICE_TYPE_ID][0]['PRICE'];
 			if($iPrice != $arPrice['MATRIX'][$PRICE_TYPE_ID][0]['DISCOUNT_PRICE']) {
 				$iPrice=$arPrice['MATRIX'][$PRICE_TYPE_ID][0]['DISCOUNT_PRICE'];
 			}
 			$sCurrency=$arPrice['MATRIX'][$PRICE_TYPE_ID][0]['CURRENCY'];
 		}

        $arOrderFields = array(
            'LID' => SITE_ID,
            'USER_ID' => $USER_ID,
            'PRICE' => $iPrice,
            'CURRENCY' => $sCurrency,
            'COMMENTS' => (LANGUAGE_ID == 'ru') ? 'Купить в 1 клик' : 'Buy 1 click'
        );
        $arOrderFields = array_merge($arOrderFields, $arOrderParams);
        if($ORDER_ID=CSaleOrder::Add($arOrderFields)) {
            if(!empty($arOrderProps)) {
                foreach($arOrderProps as $code=>$value) {
                    KonturSaleAddOrderProperty($ORDER_ID, $code, $value);
                }
            }
        }

        return $ORDER_ID;
    }
}
