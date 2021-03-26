<?php
/**
 * Created by PhpStorm.
 * User: Spawn
 * Date: 13.11.15
 * Time: 19:36
 *
 * Обработка информации об источнике посетителя
 */

class CMyUTM {
	/**
	 * Функция записывает или обновляет в куки источник перехода пользователя
	 * Данные так-же помещаются в глобальный $USER->utm
	 */
	public function SetSource(){
		global $APPLICATION;
		global $USER;
		$arUTM_new = array();

		//Проверим наличие новых переменных
		$arRef = array('yclid','gclid','utm_source','utm_medium','utm_campaign','utm_keywords','utm_keyword');
		foreach($arRef as $v){
			if(isset($_GET[$v])){ //Пришло новое значение
				if($v == 'yclid')
					$arUTM_new['clid'] = 'Директ';
				elseif($v == 'gclid')
					$arUTM_new['clid'] = 'Гугл';
				else
					$arUTM_new[str_replace('utm_','',$v)] = htmlspecialchars($_GET[$v]);
			}
		}

		//А вдруг источник был сохранён предыдушей обработкой?
		if(isset($_COOKIE['gardies-yclid-phone'])){
			setcookie('gardies-yclid-phone', "", time()-3600, '/'); //Удаляем старое
			if(isset($arUTM_new['clid'])) //Но есть новое значение, запомним откуда приходил раньше
				$arUTM_new['first'] = 'Директ';
			else
				$arUTM_new['clid'] = 'Директ';
		}

		if(!empty($arUTM_new)){ //Всё, сохраняем значения
			//Проверяем наличие сохранённых данных
			$arUTM = static::GetSource();
			if((isset($arUTM['clid']) || isset($arUTM['first'])) //Есть предыдущий лид
				&& !isset($arUTM_new['first']) //И ранее мы его не определили
			){
				//Запомним и первый переход
				$arUTM_new['first'] = $arUTM['first'] ? $arUTM['first'] : $arUTM['clid'];
			}

			$APPLICATION->set_cookie('utm',serialize($arUTM_new),time()+60*60*24*14);
			$USER->utm = $arUTM_new;
		}
	}

	/**
	 * Функция возвращает массив с сохранёнными метками utm и *clid
	 * @return array (source, campaign, medium, keywords, clid, first)
	 */
	public function GetSource(){
		global $APPLICATION;
		global $USER;
		$arUTM = array();
		if(isset($USER->utm) && is_array($USER->utm))
			$arUTM = $USER->utm;
		elseif($cUTM = $APPLICATION->get_cookie('utm'))
			$arUTM = unserialize($cUTM);
		return $arUTM;
	}
}