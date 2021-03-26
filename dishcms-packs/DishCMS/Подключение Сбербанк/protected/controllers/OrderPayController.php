<?php

/**

 * Контроллер модуля Акций 

 */

class OrderPayController extends Controller

{

    public function actionSuccess()

    {

        $title = 'Успешно';



        $this->prepareSeo($title);

        $this->breadcrumbs->add($title);



        $this->render('success');

    }



	public function actionCode($code)

	{

		$order = \DOrder\models\DOrder::model()->findByAttributes(['code' => $code]);



		$orderBundle = [];

		$orderBundle['cartItems'] = [];

		$orderBundle['cartItems']['items'] = [];



		if (!$order) {

            throw new CHttpException('404', 'Страница не найдена');

        }



        foreach ($order->getOrderData() as $item) {

        	$item['price']['value'] = (int) ($order->priceWithSale($item['price']['value']) * 100);



        	$orderBundle['cartItems']['items'][] = [

        		'positionId' => $item['id']['value'],

        		'name' => $item['title']['value'],

        		'quantity' => [

        			'value' => $item['count']['value'],

        			'measure' => 'шт.',

        		],

        		'itemDetails' => [

        			'itemDetailsParams' => [

        				[

        					'value' => 'Цвет',

        					'name' => !empty($item['id']['value']) ? $item['id']['value'] : '-',

        				],

        			],

        		],

        		'itemAmount' => $item['price']['value'] * $item['count']['value'],

        		'itemCode' => $item['id']['value'],

        		'itemPrice' => $item['price']['value'],

        		'tax' => [

        			'taxType' => 0, // без НДС

        		],

        	]; 

        }



        $error = false;



        if (Yii::app()->request->getPost('pay')) {

        	$url = "https://securepayments.sberbank.ru/payment/rest/register.do";



        	$postfields = array(

        	    "userName" => "<login-api>",

        	    "password" => "<password>",

        	    "orderNumber" => $order->id . ' - ' . $_SERVER['HTTP_HOST'],

        	    "amount" => $order->getPayTotalPrice() * 100,

        	    "returnUrl" => "http://" . $_SERVER['HTTP_HOST'] . "/orderPay/success",

        	    "taxSystem" => 2, // упрощённая, доход минус расход;

        	    "orderBundle" => json_encode($orderBundle),

        	);



        	$result = $this->rbsRequest($url, $postfields);

        	$result = json_decode($result);



        	if (is_object($result) && !isset($result->errorCode)) {



        	    $orderId = $result->orderId;

        	    $formUrl = $result->formUrl;



        	    $order->order_id = $orderId;

        	    $order->save();



        	    $this->redirect($formUrl);

        	} elseif (isset($result->errorCode)) {

        		$error = $result->errorMessage;

        	}

        }



        $title = ' Оплата заказа №' . $order->id;



        $this->prepareSeo($title);

        $this->breadcrumbs->add($title);



        $this->render('index', [

        	'order' => $order,

        	'title' => $title,

        	'error' => $error,

        ]);

	}



	protected function rbsRequestCurl($url, $postfields)

	{

		$curlOptions = array(

		    CURLOPT_RETURNTRANSFER => TRUE,

		    CURLOPT_FOLLOWLOCATION => TRUE,

		    CURLOPT_VERBOSE => TRUE,

		    CURLOPT_STDERR => $verbose = fopen('php://temp', 'rw+'),

		    CURLOPT_FILETIME => TRUE,

		    CURLOPT_POST => 1,

		    CURLOPT_POSTFIELDS => http_build_query($postfields),

		    CURLOPT_SSL_VERIFYHOST => 0,

		    CURLOPT_SSL_VERIFYPEER => 0,

		);



		$handle = curl_init($url);

		curl_setopt_array($handle, $curlOptions);

		$content = curl_exec($handle);

		echo "Verbose information:\n", !rewind($verbose), stream_get_contents($verbose), "\n";

		curl_close($handle);

		echo $content;

		die;

	}



	protected function rbsRequest($url, $postfields)

	{

	    $result = file_get_contents($url, false, stream_context_create(array(

	        'http' => array(

	            'method'  => 'POST',

	            'header'  => 'Content-type: application/x-www-form-urlencoded',

	            'content' => http_build_query($postfields),

	            'timeout'   => 60,

	        ),

	        'ssl' => array(

                'allow_self_signed' => true,

                'verify_peer' => false,

            ),

	    )));



	    return $result;

	}

}


