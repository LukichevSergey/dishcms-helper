<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class KonturPaymentsComponent extends CBitrixComponent
{
    const FORM_CRYPT_METHOD='AES-256-CFB';
    const FORM_CRYPT_KEY='kt`Q^y(p>6*r.U%fUE\X"5@u8zK&v.arBJXX[y]w^u8P6mPAj!9ZPq*f*"qn,`J!';    
    
    const MODE_TEST=1;
    const MODE_PROD=100;
    
    const SBR_WSDL='https://securepayments.sberbank.ru/payment/webservices/merchant-ws?wsdl';
    const SBR_WSDL_TEST='https://3dsec.sberbank.ru/payment/webservices/merchant-ws?wsdl';    
    const SBR_LOGIN='';
    const SBR_PASSWORD='';
    const SBR_MERCHANT='';
    const SBR_URLMODEPARAM='mode';
    
    public static function validate($value, $rule='required')
	{
	    $valid=false;
	    
	    $value=trim($value);
	    
	    switch($rule) {
	        case 'email':
	            $valid = $value && filter_var($value, FILTER_VALIDATE_EMAIL);
	            break;
	            
	        case 'phone':
	            $valid = $value && preg_match('/^([0-9()+\-]|\s+)+$/', $value) && preg_match('/^[0-9]{7,}$/', preg_replace('/[^0-9]/', '', $value));
	            break;
	        
	        case 'amount':
	            $valid = $value && is_numeric($value) && ((float)$value > 0);
                break;
                
	        case 'required':
	        default:
	            $valid = !!$value;
	    }
	    
	    return $valid;
	}
	
	/**
	 * Проведение платежа
	 * @param array $params массив параметров, вида 
	 * array(
	 *  "IS_TEST_MODE"=>Режим проведения платежа - тестовый
	 *  "URL"=>Адрес страницы с формой оплаты
	 *  "IBLOCK_ID"=>Идентификатор инфоблока платежей
	 *  "EVENT_TYPE_WAIT"=>Тип почтового события
	 *  "EVENT_ID_WAIT"=>Идентификатор почтового шаблона
	 *  "PAYMENT_STATUS_WAIT_ID"=>Идентификатор значения свойства статуса платежа "Ожидает оплаты"
	 *  "AMOUNT"=>Сумма платежа в рублях
	 *  "NAME"=>Имя плательщика
	 *  "PHONE"=>Контактный телефон плательщика
	 *  "EMAIL"=>E-Mail плательщика
	 * )
	 * @return boolean
	 */
	public static function payment($params)
	{
	    $formUrl = false;
	    
	    if(!empty($params['IBLOCK_ID']) && !empty($params['AMOUNT'])) 
	    {
	        $mode = empty($params['IS_TEST_MODE']) ? self::MODE_PROD : self::MODE_TEST;
	        $params['PHONE']=preg_replace('/[^+0-9]/', '', $params['PHONE']);
	        $params['AMOUNT']=(float)$params['AMOUNT'];
	        
    	    $arFields=[
    	        'IBLOCK_ID'=>$params['IBLOCK_ID'],
    	        'NAME'=>$params['NAME'],
    	        'ACTIVE'=>'Y',
    	        'PROPERTY_VALUES'=>[
    	            'AMOUNT'=>$params['AMOUNT'],
    	            'PHONE'=>$params['PHONE'],
    	            'EMAIL'=>$params['EMAIL'],
    	            'STATUS'=>['VALUE'=>$params['PAYMENT_STATUS_WAIT_ID']]
    	        ]
    	    ];
    	    
    	    $el=new \CIBlockElement();
    	    if($orderId=$el->Add($arFields)) {
    	        $paymentStatus=\CIBlockPropertyEnum::GetByID($params['PAYMENT_STATUS_WAIT_ID']);
    	        
    	        $description="{$params['NAME']}, {$params['PHONE']}, {$params['EMAIL']}";
    	        $response = static::sbrRegisterOrder($mode, $orderId, $params, $description);
    	        
    	        if($response->errorCode === 0) {
    	            \CIBlockElement::SetPropertyValueCode($orderId, 'PAYMENT_ID', $response->orderId);
    	            $formUrl=$response->formUrl;
    	        }
    	            
    	        if(!empty($params['EVENT_TYPE_WAIT']) && !empty($params['EVENT_ID_WAIT'])) {
    	            \CEvent::Send($params['EVENT_TYPE_WAIT'], SITE_ID, [
    	               'ORDER_ID'=>$orderId,
        	           'DATE'=>date('d.m.Y H:i:s'),
        	           'NAME'=>$params['NAME'],
    	               'AMOUNT'=>$params['AMOUNT'],
    	               'PHONE'=>$params['PHONE'],
        	           'EMAIL'=>$params['EMAIL'],
        	           'STATUS'=>$paymentStatus['VALUE']
        	       ], 'Y', $params['EVENT_ID_WAIT']);
        	    }
    	    }
	    }
	    
	    return $formUrl;
	}
	
	/**
	 * 
	 * @param array $arParams
	 */
	public static function checkPayment($arParams)
	{
	    if(!empty($_REQUEST['orderId'])) {
	        $rs=\CIBlockElement::GetList(
	            ['SORT'=>'ASC'], 
	            ['IBLOCK_ID'=>$arParams['IBLOCK_ID'], 'PROPERTY_PAYMENT_ID'=>$_REQUEST['orderId']], 
	            false, 
	            false, 
	            ['ID', 'IBLOCK_ID', 'NAME', 'DATE_CREATE', 'PROPERTY_AMOUNT', 'PROPERTY_PHONE', 'PROPERTY_EMAIL', 'PROPERTY_STATUS']
	        );
	        if($order=$rs->Fetch()) {
	            $mode=(!empty($_REQUEST['mode']) && ((int)$_REQUEST['mode']==self::MODE_TEST)) ? self::MODE_TEST : self::MODE_PROD;
	            $response = static::sbrGetOrderStatusExtended($mode, $_REQUEST['orderId']);
	            if( !($response instanceof \SoapFault) ) {
	                $arEventParams=[
	                    'ORDER_ID'=>$order['ID'],
	                    'DATE'=>$order['DATE_CREATE'],
	                    'NAME'=>$order['NAME'],
	                    'AMOUNT'=>$order['PROPERTY_AMOUNT_VALUE'],
	                    'PHONE'=>$order['PROPERTY_PHONE_VALUE'],
	                    'EMAIL'=>$order['PROPERTY_EMAIL_VALUE'],
	                ];
	                
	                if($response->orderStatus === 2) {
	                    if($order['PROPERTY_STATUS_ENUM_ID'] != $arParams['PAYMENT_STATUS_PAID_ID']) {
    	                    \CIBlockElement::SetPropertyValueCode($order['ID'], 'STATUS', $arParams['PAYMENT_STATUS_PAID_ID']);
    	                    
    	                    if(!empty($arParams['EVENT_TYPE_PAID']) && !empty($arParams['EVENT_ID_PAID'])) {
    	                        $paymentStatus=\CIBlockPropertyEnum::GetByID($arParams['PAYMENT_STATUS_PAID_ID']);
    	                        $arEventParams['STATUS']=$paymentStatus['VALUE'];
    	                        \CEvent::Send($arParams['EVENT_TYPE_PAID'], SITE_ID, $arEventParams, 'Y', $arParams['EVENT_ID_PAID']);
    	                    }
	                    }
	                    
	                    return true;
	                }
	                elseif(in_array($response->orderStatus, [3, 4, 6])) {
	                    if($order['PROPERTY_STATUS_ENUM_ID'] != $arParams['PAYMENT_STATUS_FAIL_ID']) {
    	                    \CIBlockElement::SetPropertyValueCode($order['ID'], 'STATUS', $arParams['PAYMENT_STATUS_FAIL_ID']);
    	                    
    	                    if(!empty($arParams['EVENT_TYPE_FAIL']) && !empty($arParams['EVENT_ID_FAIL'])) {
    	                        $paymentStatus=\CIBlockPropertyEnum::GetByID($params['PAYMENT_STATUS_FAIL_ID']);
    	                        $arEventParams['STATUS']=$paymentStatus['VALUE'];
    	                        \CEvent::Send($arParams['EVENT_TYPE_FAIL'], SITE_ID, $arEventParams, 'Y', $arParams['EVENT_ID_FAIL']);
    	                    }
	                    }
	                    
	                    return false;
	                }
	            }
	            
	        }
	    }
	    
	    return null;
	}
	
	public static function encrypt($data)
	{
	    return openssl_encrypt(json_encode($data), self::FORM_CRYPT_METHOD, self::FORM_CRYPT_KEY);
	}
	
	public static function decrypt($str)
	{
	    return json_decode(openssl_decrypt($str, self::FORM_CRYPT_METHOD, self::FORM_CRYPT_KEY), true);
	}
	
	private static function sbrGetWsdlUrl($mode)
	{
	    switch($mode) {
	        case self::MODE_TEST:
	            return self::SBR_WSDL_TEST;
	            break;
	        
	        case self::MODE_PROD:
	        default:
	            return self::SBR_WSDL;
	    }	    
	}
	
	private static function sbrSoapClient($mode=null)
	{
	    $client=new \SoapClient(static::sbrGetWsdlUrl($mode), ["cache_wsdl" => 0, "trace" => 1, "exceptions" => 0]);
	    $client->__setSoapHeaders(static::getSoapHeaderWSSecurity());
	    
	    return $client;
	}
	
	private static function sbrGetOrderStatusExtended($mode, $orderId)
	{
	    $client=static::sbrSoapClient($mode);
	    
	    try {
    	    return $client->getOrderStatusExtended([
    	        'orderId'=>$orderId
    	    ]);
	    }
	    catch(\SoapFault $e) {
	        return false;
	    }
	}
	
	/**
	 * Проведение платежа (Сбербанк)
	 * 
	 * @param integer $mode режим проведения платежа (тестовый, боевой)
	 * @param integer $orderId идентификатор заказа на сайте
	 * @param array $params массив дополнительных параметров вида:
	 * array(
	 *  "AMOUNT"=>(float) Сумма платежа в рублях (обязательный параметр)
	 *  "URL"=>Абсолютная ссылка на страницу с компонентом платежной формы 
	 *  "EMAIL"=>E-Mail плательщика
	 * )
	 * @param string $description дополнительное описание для платежа
	 * @return stdClass|false результат запроса на проведение платежа.
	 */
	private static function sbrRegisterOrder($mode, $orderId, $params, $description='')
	{
	    if(empty($params['URL']) || empty($params['AMOUNT'])) {
	        return false;
	    }
	    
	    $client=static::sbrSoapClient($mode);
	    
	    // ?sbr=success&orderId=513610f0-0dd2-7e97-9794-9b3804b23fe1&lang=ru
	    $hasUrlQS = (strpos($params['URL'], '?') !== false);
	    $orderParams=[
	        'language'=>'ru',
	        'merchantLogin'=>self::SBR_MERCHANT,
	        'merchantOrderNumber'=>$orderId,
	        'amount'=>$params['AMOUNT'] * 100,
	        'returnUrl'=>$params['URL'] . ($hasUrlQS ? '&' : '?') . self::SBR_URLMODEPARAM . '=' . $mode,
	        'failUrl'=>$params['URL']  . ($hasUrlQS ? '&' : '?') . self::SBR_URLMODEPARAM . '=' . $mode,
	    ];
	    if(!empty($description)) {
	        $orderParams['description']=$description;
	    }
	    if(!empty($params['EMAIL'])) {
	        $orderParams['params']=['email'=>$params['EMAIL']];
	    }
	    
	    try {
	       $response = $client->registerOrder($orderParams);
	    }
	    catch(\SoapFault $e) {
	        return false;
	    }
	    
	    return $response;
	}
	
	/**
	 * Составляем header xml запроса
	 *
	 * @return SoapHeader
	 */
	private static function getSoapHeaderWSSecurity()
	{
	    //namespaces
	    $nsWsse = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
	    $nsWsu = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
	    $passwordType = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText';
	    
	    //формируем xml
	    $root = new SimpleXMLElement('<root/>');
	    $root->registerXPathNamespace('wsse', $nsWsse);
	    
	    $security = $root->addChild('wsse:Security', null, $nsWsse);
	    $security->registerXPathNamespace('wsu', $nsWsu);
	    
	    $usernameToken = $security->addChild('wsse:UsernameToken', null, $nsWsse);
	    $usernameToken->addChild('wsse:Username', self::SBR_LOGIN, $nsWsse);
	    $usernameToken->addChild('wsse:Password', self::SBR_PASSWORD, $nsWsse)->addAttribute('Type', $passwordType);
	    
	    //берем из xml только security
	    
	    $securityXml = $root->xpath('/root/wsse:Security');
	    
	    //формируем заголовок
	    return new SoapHeader($nsWsse, 'Security', new SoapVar($securityXml[0]->asXML(), XSD_ANYXML), true);
	}
}
