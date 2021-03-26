<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class KonturPaymentsComponent extends CBitrixComponent
{
    const FORM_CRYPT_METHOD='AES-256-CFB';
    const FORM_CRYPT_KEY='7fL%2M5am4Z4:jJmsZn;"96%P/}EmRkCULVj$pbhrP$:@!rH;hFdn_g#j:ww6>8y';    
    
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
                
	        case 'date':
	            $valid = $value && preg_match('#^[0-3][0-9]/[0-1][0-9]/[1-2][0-9]{3}$#', $value);
                break;
            
	        case 'safe':
	            $valid = true;
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
	public static function ypayment($arParams, $arData)
	{
	    $formUrl = false;
	    
	    if(!empty($arParams['IBLOCK_ID']) && !empty($arData['amount'])) 
	    {
	        $mode = ($arParams['PAYMENT_TEST_MODE'] == 'Y') ? self::MODE_TEST : self::MODE_PROD;
	        
	        $arData['phone']=preg_replace('/[^+0-9]/', '', $arData['phone']);
	        
	        $arFields=[
    	        'IBLOCK_ID'=>$arParams['IBLOCK_ID'],
    	        'NAME'=>$arData['name'],
    	        'ACTIVE'=>'Y',
    	        'PROPERTY_VALUES'=>[
    	            'AMOUNT'=>$arData['amount'],
    	            'PHONE'=>$arData['phone'],
    	            'EMAIL'=>$arData['email'],
    	            'PASSPORT_NUMBER'=>$arData['passport_number'],
    	            'PASSPORT_DATE'=>$arData['passport_date'],
    	            'PASSPORT_ORG'=>$arData['passport_org'],
    	            'PASSPORT_ADDRESS'=>$arData['passport_address'],
    	            'DATE'=>$arData['date']
    	        ]
    	    ];
	        
	        if(!empty($arData['promocode'])) {
	            $arFields['PROPERTY_VALUES']['PROMOCODE']=$arData['promocode'];
	        }
	        
	        $arFields['PROPERTY_VALUES']['CREDITORS']=[];
	        foreach($arData['creditors'] as $creditor) {
	            $arFields['PROPERTY_VALUES']['CREDITORS'][] = 'Наименование Кредитора: ' . $creditor['creditor_name'] . "\n"
	                . 'Номер кредитного договора: ' . $creditor['creditor_number'] . "\n"
	                . 'Дата кредитного договора: ' . $creditor['creditor_date'] . "\n"
	                . 'Адрес кредитора: ' . $creditor['creditor_address'];
	                
				$arFields['PROPERTY_VALUES']['CREDITORS_FOR_DOC'][] = 'Кредитный договор №' . $creditor['creditor_number'] . ' от ' . str_replace('/', '.', $creditor['creditor_date']) . ' г.';
	        }
    	    
	        $error=null;
    	    if($orderId=static::add($arFields, $error)) {
    	        static::updateStatus($arParams, $orderId, $arParams['PAYMENT_STATUS_WAIT_ID']);
    	        
    	        $formUrl=urlencode(static::encrypt(['IBLOCK_ID'=>$arParams['IBLOCK_ID'], 'ORDER_ID'=>$orderId]));
    	    }
    	    else {
    	        //$formUrl=$error;
    	    }
	    }
	    
	    return $formUrl;
	}
	
	protected static function add($arFields, &$error)
	{
	    $el=new \CIBlockElement();
	    
	    if($id=$el->Add($arFields)) {
	        return $id;
	    }
	    
	    $error='error' . $el->LAST_ERROR;
	    
	    return false;
	}
	
	public static function getOrder($arFilter)
	{
	    $rs=\CIBlockElement::GetList(['SORT'=>'ASC'], $arFilter);
	    if($order=$rs->Fetch()) {
	        $order['PROPERTIES']=[];
	        $rs=\CIBlockElement::GetProperty($order['IBLOCK_ID'], $order['ID']);
	        while($prop=$rs->Fetch()) {
	        	if(in_array($prop['CODE'], ['CREDITORS_FOR_DOC', 'CREDITORS'])) {
	        		if(!isset($order['PROPERTIES'][$prop['CODE']])) {
	        			$order['PROPERTIES'][$prop['CODE']]=[];
	        		}
	        		$order['PROPERTIES'][$prop['CODE']][]=$prop;
	        	}
	        	else {
		            $order['PROPERTIES'][$prop['CODE']]=$prop;
		        }
	        }
	    }
	    return $order;
	}
	
	public static function updateStatus($arParams, $orderId, $paymentStatusValueId, $isPaymentId=false, $sendDisclaimerDoc=false)
	{
	    $arFilter=['IBLOCK_ID'=>$arParams['IBLOCK_ID']];
	    
	    if($isPaymentId) $arFilter['PROPERTY_PAYMENT_ID']=$orderId;
	    else $arFilter['ID']=$orderId;
	    
	    $rs=\CIBlockElement::GetList(['SORT'=>'ASC'], $arFilter);
	    if($order=static::getOrder($arFilter)) {
	        \CIBlockElement::SetPropertyValueCode($order['ID'], 'PAYMENT_STATUS', $paymentStatusValueId);
	        
	        $paymentStatus=\CIBlockPropertyEnum::GetByID($paymentStatusValueId);
	        
	        $arFiles=[];
	        $arEventFields = [
	            'SUBJECT'=>"Заявка №{$order['ID']}. {$paymentStatus['VALUE']}",
	            'ORDER_ID'=>$order['ID'],
    	        'DATE'=>$order['DATE_CREATE'],
    	        'PAYMENT_STATUS'=>$paymentStatus['VALUE'],
    	        'AMOUNT'=>$order['PROPERTIES']['AMOUNT']['VALUE'],
    	        'NAME'=>$order['NAME'],
    	        'PHONE'=>$order['PROPERTIES']['PHONE']['VALUE'],
    	        'EMAIL'=>$order['PROPERTIES']['EMAIL']['VALUE'],
    	        /*
    	        'PASSPORT_NUMBER'=>$order['PROPERTIES']['PASSPORT_NUMBER']['VALUE'],
    	        'PASSPORT_DATE'=>$order['PROPERTIES']['PASSPORT_DATE']['VALUE'],
    	        'PASSPORT_ORG'=>$order['PROPERTIES']['PASSPORT_ORG']['VALUE'],
    	        'PASSPORT_ADDRESS'=>$order['PROPERTIES']['PASSPORT_ADDRESS']['VALUE'],
    	        'DATE'=>$order['PROPERTIES']['DATE']['VALUE'],
    	        **/
    	        'PROMOCODE'=>'-'
	        ];
	        
	        if(!empty($order['PROPERTIES']['PROMOCODE']['VALUE'])) {
	            $arEventFields['PROMOCODE']=$order['PROPERTIES']['PROMOCODE']['VALUE'];
	        }
	        
	        if($sendDisclaimerDoc) {
	            $disclaimerDocFile=static::attachDisclaimerDoc($order);
	            $rs=\CIBlockElement::GetProperty($arParams['IBLOCK_ID'], $order['ID'], ['NAME'=>'ASC'], ['CODE'=>'DISCLAIMER_FILE']);
	            $file=$rs->Fetch();
	            $arFiles[]=$file['VALUE'];
	            
	            $arClientEventFields=[
	                'SUBJECT'=>"Заявление №{$order['ID']} об отказе от взаимодействия",
	                'ORDER_ID'=>$order['ID'],
    	            'DATE'=>$order['DATE_CREATE'],
    	            'MAIL_TO'=>$order['PROPERTIES']['EMAIL']['VALUE']
	            ];
	            $eventId=$arParams['EVENT_ID'];
	            $arParams['EVENT_ID']=$arParams['CLIENT_EVENT_ID'];
	            static::send($arParams, $arClientEventFields, $arFiles);
	            $arParams['EVENT_ID']=$eventId;
	        }
	        static::send($arParams, $arEventFields, $arFiles);
	    }
	}
	
	protected static function attachDisclaimerDoc($order)
	{
		$templateFilename = dirname(__FILE__) . '/docs/disclaimer_doc_tpl.docx';
		$outputFilename = $_SERVER['DOCUMENT_ROOT'].'/upload/disclaimers/' . $order['ID'] . '.docx';
		
		$monthNames=[
			'01'=>'января', 
			'02'=>'февраля', 
			'03'=>'марта', 
			'04'=>'апреля', 
			'05'=>'мая', 
			'06'=>'июня', 
			'07'=>'июля', 
			'08'=>'августа', 
			'09'=>'сентября', 
			'10'=>'октября', 
			'11'=>'ноября', 
			'12'=>'декабря'
		];
		
		list($ptCode, $ptNumber)=explode(' ', $order['PROPERTIES']['PASSPORT_NUMBER']['VALUE']);
		list($ptDay, $ptMonth, $ptYear)=explode('/', $order['PROPERTIES']['PASSPORT_DATE']['VALUE']);
		$ptMonthName=$monthNames[$ptMonth];
		list($dateDay, $dateMonth, $dateYear)=explode('/', $order['PROPERTIES']['DATE']['VALUE']);
		$dateMonthName=$monthNames[$dateMonth];
		
		$arFields = [
			'CREDITOR_NAME'=>$order['NAME'],
			'CLIENT_NAME'=>$order['NAME'],
			'PASSPORT_CODE'=>$ptCode,
			'PASSPORT_NUMBER'=>$ptNumber,
			'PASSPORT_DATE_DAY'=>$ptDay,
			'PASSPORT_DATE_MONTH'=>$ptMonthName,
			'PASSPORT_DATE_YEAR'=>$ptYear,
			'PASSPORT_ORG'=>$order['PROPERTIES']['PASSPORT_ORG']['VALUE'],
			'CLIENT_ADDRESS'=>$order['PROPERTIES']['PASSPORT_ADDRESS']['VALUE'],
			'CLIENT_PHONE'=>$order['PROPERTIES']['PHONE']['VALUE'],
			'CLIENT_EMAIL'=>$order['PROPERTIES']['EMAIL']['VALUE'],
			'CREDITORS'=>'',
			'DATE_DAY'=>$dateDay,
			'DATE_MONTH'=>$dateMonthName,
			'DATE_YEAR'=>$dateYear,
		];
		
		$n=1;
		foreach($order['PROPERTIES']['CREDITORS_FOR_DOC'] as $creditor) {
			if(!empty($creditor['VALUE'])) {
				$arFields['CREDITORS'] .= ($n++) . '. ' . $creditor['VALUE']."\n";
			}
		}
		
		static::attachDoc($templateFilename, $outputFilename, $arFields);
		
		$arFile=CFile::MakeFileArray($outputFilename);
		
		\CIBlockElement::SetPropertyValueCode($order['ID'], 'DISCLAIMER_FILE', $arFile);
		
		return $arFile;
	}
	
	protected static function attachDoc($templateFilename, $outputFilename, $values=[])
	{
		spl_autoload_register(function($class){
			$filename=dirname(__FILE__).'/vendors/' . str_replace('\\', '/', trim($class, '\\')) . '.php';
			if(is_file($filename)) {
				include $filename;
			}
		});
		
		$PHPWord = new \PhpOffice\PhpWord\PhpWord();
		$document = $PHPWord->loadTemplate($templateFilename);
		foreach($values as $name=>$value) {
			$document->setValue($name, $value);
		}
		
//		file_put_contents(dirname(__FILE__).'/log.log', $outputFilename."\n\n", FILE_APPEND);
		$document->saveAs($outputFilename);
	}
	
	protected static function send($arParams, $arFields, $arFiles=[])
	{
	    if(!empty($arParams['EVENT_TYPE']) && !empty($arParams['EVENT_ID'])) {
            \CEvent::Send($arParams['EVENT_TYPE'], SITE_ID, $arFields, 'Y', $arParams['EVENT_ID'], $arFiles);
	    }
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
