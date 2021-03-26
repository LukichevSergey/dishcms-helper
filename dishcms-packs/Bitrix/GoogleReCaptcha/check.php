<?
		check_bitrix_sessid();

    	$request = new \Bitrix\Main\Web\HttpClient(); //Создает объект HttpClient

		$settings = COption::GetOptionString("twim.recaptchafree", "settings", false, SITE_ID);
        $arSettings = unserialize($settings);
    	//Формируем запрос на проверку в Google
    	$post = $request->post("https://www.google.com/recaptcha/api/siteverify", Array(
     	   	"secret" => $arSettings["secretkey"], //Наш секретный ключ от Google
        	"response" => $data["g-recaptcha-response"], //Сам хеш с формы
	        "remoteip" => $_SERVER["REMOTE_ADDR"] //IP адрес пользователя проходящего проверку
    	));
	    $post = json_decode($post); //Декодируем ответ от Google
		if ($post->success == 'true') {
			$hasErrors=false;
		}
		else {
			echo 'ecaptcha';
			return;
		}
