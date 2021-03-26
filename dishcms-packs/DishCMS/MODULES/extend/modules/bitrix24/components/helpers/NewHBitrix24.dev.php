<?php
namespace extend\modules\bitrix24\components;

use common\components\helpers\HYii as Y;

/********************************************************
Пример создания лида
			
<?php 
$bx24 = new Bitrix24('ид_пользователя', 'хэш_вебхука');

$bx24->createLead([
	'NAME' => 'КОНТУР',
	'EMAIL_WORK' => 'info@kontur-lite.ru',
	'EMAIL_HOME' => 'info@kontur-nsk.ru',
	'PHONE_WORK' => '8 (383) 255-33-33'
], 123); ?>

		
Пример получения доступных полей для создания лида
			
<?php 
$bx24 = new Bitrix24('ид_пользователя', 'хэш_вебхука');

$bx24->getLeadFields(); ?>

*********************************************************/

class Bitrix24
{
	/**
	 * Идентификатор пользователя вебхука
	 * @var int
	 */
	private $userId;

	/**
	 * Хэш вебхука
	 * @var string
	 */
	private $webhook;

	/**
	 * Конструктор класса
	 * @param int $userId идентификатор пользоателя вебхука
	 * @param string $webhook хэш вебхука
	 */
	public function __construct($userId, $webhook)
	{
		$this->userId = (int)$userId;
		$this->webhook = $webhook;
	}

	/**
	 * Получение URL метода вебхука
	 * @param string $action имя метода
	 * @return string
	 */
	public function getRestUrl($action)
	{
		return "https://kontur.bitrix24.ru/rest/{$this->userId}/{$this->webhook}/{$action}";
	}

	/**
	 * Получение списка доступных полей
	 *
	 * @return []
	 */
	public function getLeadFields()
	{
		return $this->send('crm.lead.fields.json');
	}

	/**
	 * Создание лида
	 *
	 * @param [] $fields Поля для создания лида вида [name=>value].
	 * @param int|null $assignedById Идентификатор отвественного пользователя. 
	 * По умолчанию (null) не назначен.
	 * @param bool $sonet произвести регистрацию события добавления лида в живой ленте. 
	 * По умолчанию (true) - произвести регистрацию события.
	 * @return [] результат добавления лида
	 */
	public function createLead($fields, $assignedById = null, $sonet = true)
	{
		// нормализация данных
		$normalizedFields = [];
		foreach ($fields as $key => $value) {
			if (preg_match('/^(?<name>EMAIL|PHONE|IM|WEB)_(?<type>.*)$/', strtoupper($key), $m)) {
				$normalizedFields[$m['name']]['n' . count($normalizedFields[$m['name']] ?? [])] = [
					'VALUE' => ($m['name'] == 'PHONE')
						? ('+' . preg_replace(['/[^0-9]+/', '/^8/'], ['', '7'], $value))
						: trim($value),
					'VALUE_TYPE' => $m['type']
				];
			} else {
				$normalizedFields[$key] = trim($value);
			}
		}

		// Если статус заявки не передан явно, устанавливаем его в значение "NEW" (новая)
		$normalizedFields['STATUS_ID'] = $normalizedFields['STATUS_ID'] ?? 'NEW';

		// Если источник заявки не передан явно, устанавливаем его в значение "WEB" (веб-сайт)
		$normalizedFields['SOURCE_ID'] = $normalizedFields['SOURCE_ID'] ?? 'WEB';

		if ($assignedById) {
			$normalizedFields['ASSIGNED_BY_ID'] = $assignedById;
		}

		// выполение запроса на создание лида
		return $this->send('crm.lead.add.json', [
			'fields' => $normalizedFields,
			'params' => $sonet ? ['REGISTER_SONET_EVENT' => 'Y'] : []
		]);
	}

	/**
	 * Отправка запроса
	 *
	 * @param string $action имя метода
	 * @param [] $data дополнительные данные. По умолчанию [] (пустой массив).
	 * @return [] в случае успеха, возвращает массив JSON декодированных данных.
	 */
	public function send($action, $data = [])
	{
		$ch = curl_init($this->getRestUrl($action));

		curl_setopt_array($ch, [
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_HEADER => 0,
			CURLOPT_POSTFIELDS => http_build_query($data),
		]);

		if (!$result = curl_exec($ch)) {
			trigger_error(curl_error($ch));
		} elseif (!$result = json_decode($result, true)) {
			trigger_error(json_last_error());
		} elseif (array_key_exists('error', $result)) {
			trigger_error($result['error_description']);
		}

		return $result;
	}
}