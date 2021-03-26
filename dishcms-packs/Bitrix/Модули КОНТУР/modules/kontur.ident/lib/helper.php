<?php
namespace Kontur\Ident;

use Bitrix\Main;
use Bitrix\Main\Config\Option;

class Helper
{
    /**
     * Идентификатор главной конфигурации модуля
     * @var string 
     */
    const CONFIG_MAIN_ID='main';

    /**
     * Статус "Новая заявка".
     * @var int
     */
    const STATUS_TICKET_NEW=0;

    /**
     * Статус "Заявка отправлена в IDENT".
     * @var int
     */
    const STATUS_TICKET_DONE=100;

    /**
     * Создание новой заявки
     *
     * @param [] $fields поля заявки доступные в TicketTable
     * @return TicketTable|false в случае успешного создания 
     * возвращает объект TicketTable, иначе возвращает false.
     */
    public static function createTicket($fields)
    {
        $ticket = new TicketTable;

        if(empty($fields['FORM_NAME'])) {
            $fields['FORM_NAME']=static::param('DEFAULT_FORM_NAME');
        }
        
        $result=$ticket->add(compact('fields'));

        return $result->isSuccess() ? $ticket : false;
    }

    /**
	 * Получить UUID v4
	 *
	 * @link https://stackoverflow.com/a/44504979
	 * @link https://stackoverflow.com/a/55439684
	 * 
	 * @return string
	 */
    public static function guid()
    {
        if (function_exists('com_create_guid') === true) {
			return trim(com_create_guid(), '{}');
		}

		$data = PHP_MAJOR_VERSION < 7 ? openssl_random_pseudo_bytes(16) : random_bytes(16);
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // Set version to 0100
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // Set bits 6-7 to 10

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Проверяет является ли текущий пользователь администратором
     *
     * @return boolean
     */
    public static function isAdmin()
    {
        global $USER;
        return ($USER instanceof \CUser) && $USER->IsAdmin();
    }

    /**
     * Проверяет разрешение на доступ
     *
     * @param boolean $throwException выбрасывать исключение. По умолчанию (true) 
     * если доступ запрещен будет выбрашено ислючение. Если в параметре передать 
     * false, то при запрещенном доступе будет возвращено false.
     * @return bool
     */
    public static function checkAccess($throwException=true)
    {
        if(static::isAdmin() || \CSite::InGroup(static::param('USERS_GROUP_ID', []))) {
            return true;
        }
        
        if($throwException) {
            throw new Main\SystemException('Доступ запрещен. Обратитесь к администратору сайта');
        }

        return false;
    }

    /**
     * Получить значение опции из настроек модуля
     *
     * @param string $name имя параметра
     * @param mixed $default значение по умолчанию. По умолчанию null.
     * @param mixed $siteId идентификатор сайта. По умолчанию false.
     * @return mixed
     */
    public static function option($name, $default=null, $siteId=false)
    {
        return Option::get('kontur.ident', $name, $default, $siteId);
    }

    /**
     * Получить значение параметра основной конфигурации
     *
     * @param string $name имя параметра
     * @param mixed $default значение по умолчанию. По умолчанию null.
     * @return mixed
     */
    public static function param($name, $default=null)
    {
    	return Config::getInstance(self::CONFIG_MAIN_ID)->get($name, $default);
    }
}