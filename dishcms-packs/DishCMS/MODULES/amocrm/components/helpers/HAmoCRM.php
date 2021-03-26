<?php
namespace amocrm\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HTools;
use settings\components\helpers\HSettings;
use crud\models\ar\amocrm\models\Token;
use AmoCRM\OAuth2\Client\Provider\AmoCRM;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Grant\RefreshToken;

class HAmoCRM
{
    const WITH_CUSTOM_FIELDS='custom_fields';
    
    const TASK_TYPE_CALL=1;
    const TASK_TYPE_MEET=2;
    const TASK_TYPE_LETTER=3;
    
    const TASK_ELEMENT_TYPE_CONTACT=1;
    const TASK_ELEMENT_TYPE_DEAL=2;
    const TASK_ELEMENT_TYPE_COMPANY=3;
    const TASK_ELEMENT_TYPE_CUSTOMER=12;
    
    /**
     * Получить настройки модуля AmoCRM
     * @return \amocrm\models\AmoCrmSettings
     */
    public static function settings()
    {
        return HSettings::getById('amocrm');
    }
    
    /**
     * Получить идентификатор соответствия дополнительного поля 
     * из настроек модуля AmoCRM 
     * @param string $name сокращенное имя поля.
     * @return int|null
     */
    public static function getFieldId($name)
    {
        try {
            return static::settings()->{$name . '_field_id'};
        }
        catch (\Exception $e) {
            
        }
        
        return null;
    }
    
    /**
     * Получить токен авторизации
     * @return \League\OAuth2\Client\Token\AccessTokenInterface|null
     */
    public static function getAccessToken()
    {
        $accessToken=null;
        
        try {
            $provider=static::getProvider();
            
            if(static::isNewIntegration()) {
                $accessToken=$provider->getAccessToken('authorization_code', [
                    'code'=>static::settings()->auth_code
                ]);
                
                static::saveToken($accessToken);
            }
            
            if(static::tokenIsExpire()) {
                $accessToken=$provider->getAccessToken(new RefreshToken(), [
                    'refresh_token'=>Token::getRefreshToken(static::getClientId()),
                ]);
            }
            else {
                $accessToken=static::getAccessTokenObject();
            }
        }
        catch(\Exception $e) {
            $accessToken=null;
        }
        
        return $accessToken;
    }
    
    /**
     * Получить объект токена авторизации
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public static function getAccessTokenObject()
    {
        return new AccessToken([
            'access_token' => Token::getAccessToken(static::getClientId()),
            'refresh_token' => Token::getRefreshToken(static::getClientId()),
            'expires' => Token::getExpire(static::getClientId()),
            'baseDomain' =>static::getBaseDomain(),
        ]);
    }
    
    /**
     * Получить провайдер соединения с API AmoCRM.
     * @return \AmoCRM\OAuth2\Client\Provider\AmoCRM
     */
    public static function getProvider()
    {
        Y::module('amocrm');
        
        return new AmoCRM([
            'clientId'=>static::getClientId(),
            'clientSecret'=>static::settings()->client_secret,
            'redirectUri'=>static::settings()->redirect_uri,
            'baseDomain'=>static::getBaseDomain()
        ]);
    }
    
    /**
     * Получить базовый домен для API
     * @return string
     */
    public static function getBaseDomain()
    {
        return static::settings()->account . '.amocrm.ru';
    }
    
    /**
     * Получить идентификатор интеграции
     * @return string
     */
    public static function getClientId()
    {
        return (string)static::settings()->client_id;
    }
    
    /**
     * Новая интеграция
     * @return boolean
     */
    public static function isNewIntegration()
    {
        return (Token::isExpire(static::getClientId()) === null);
    }
    
    /**
     * Срок действия токена закончился
     * @return bool
     */
    public static function tokenIsExpire()
    {
        return Token::isExpire(static::getClientId());
    }
    
    /**
     * Сохранение токена
     * @param \League\OAuth2\Client\Token\AccessTokenInterface $accessToken токен авторизации
     * @return bool
     */
    public static function saveToken($accessToken)
    {
        $token=Token::getToken(static::getClientId());
        if(!$token) {
            $token=new Token;
            $token->client_id=static::getClientId();
        }
        
        $token->access_token=$accessToken->getToken();
        $token->refresh_token=$accessToken->getRefreshToken();
        $token->expire=$accessToken->getExpires();
        
        return $token->save();
    }
    
    /**
     * Получить данные аккаунта AmoCRM
     * @param string|[] $with ключ или ключи (массивом) получения дополнительной информации (через запятую).
     * Подробнее https://www.amocrm.ru/developers/content/api/account#values
     * @return []|false
     */
    public static function getAccount($with=[])
    {
        return static::apiGet('api/v2/account', ['with'=>implode(',', A::toa($with))]);
    }
    
    /**
     * Получить дополнительные поля.
     * @param string $type тип группы для которой получаются 
     * дополнительные поля. По умолчанию "contacts".
     * @return []
     */
    public static function getCustomFields($type='contacts')
    {
        $fields=[];
        
        if($account=static::getAccount(self::WITH_CUSTOM_FIELDS)) {
            if(!empty($account['_embedded']['custom_fields'][$type])) {
                $fields=$account['_embedded']['custom_fields'][$type];
            }
        }
        
        return $fields;
    }
    
    /**
     * Получить дополнительные поля в формате listData.
     * @param string $type тип группы для которой получаются
     * дополнительные поля. По умолчанию "contacts".
     * @return []
     */
    public static function getCustomFieldsListData($type='contacts')
    {
        $data=[];
        
        $fields=static::getCustomFields($type);
        foreach($fields as $id=>$field) {
            $data[$id]=$field['name'];
        }
        
        return $data;
    }
    
    /**
     * Добавление новой задачи
     * @link https://www.amocrm.ru/developers/content/api/tasks
     * @param string|[] $elementId Уникальный идентификатор контакта или сделки.
     * Может быть передан массив параметров для HAmoCRM::newContact(), в этом 
     * случае контакт будет добавлен.
     * @param string $elementType Тип привязываемого элемента (1 – контакт, 2- сделка, 3 – компания, 12 – покупатель).
     * @param string $taskType тип задачи
     * @param [] $task дополнительные параметры для задачи для секции "add"
     * @return [] массив добавленных задач
     */
    public static function newTask($elementId, $elementType, $taskType, $task=[])
    {
        $tasks=[];
        
        if(is_array($elementId)) {
            $elements=A::toa(static::newContact($elementId));
        }
        else {
            $elements=A::toa($elementId);
        }
        
        foreach($elements as $elementId) {
            $task=static::apiPost('api/v2/tasks', [
                'add'=>[A::m([
                    'element_id'=>$elementId,
                    'element_type'=>$elementType,
                    'task_type'=>$taskType,
                    'text'=>'...',
                    'created_at'=>time(),
                    'updated_at'=>time(),
                ], $task)
            ]]);
            
            if($task) {
                $tasks[]=$task;
            }
        }
        
        return $tasks;
    }
    
    /**
     * Получить идетификатор контакта.
     * @param string $phone (обязательно) номер телефона. По данному полю будет осуществлен поиск.
     * Если контакт будет найден, будет возвращен идентификатор найденного контакта. 
     * Будет выполнена нормализация номера телефона.
     * @param string $name Имя контакта. Обязательно, если требуется создать новый контакт.
     * @param string $email E-Mail контакта
     * @return string|null идентификатор контакта
     */
    public static function getContactId($phone, $name=null, $email=null)
    {
        $name=trim((string)$name);
        $email=trim((string)$email);
        $phone=HTools::normalizePhone($phone);
        
        if(!empty($phone)) {
            $phone='+' . $phone;
            
            $contact=HAmoCRM::getContact($phone);
            
            if(!empty($contact['_embedded']['items'][0]['id'])) {
                return (string)$contact['_embedded']['items'][0]['id'];
            }
            elseif(!empty($name)) {
                $contact=[
                    'name'=>$name,
                    'custom_fields'=>[]
                ];
                
                if(HAmoCRM::getFieldId('phone')) {
                    $contact['custom_fields'][]=['id'=>HAmoCRM::getFieldId('phone'), 'values'=>[['value'=>$phone, 'enum'=>'MOB']]];
                }
                
                if(!empty($email) && HAmoCRM::getFieldId('email')) {
                    $contact['custom_fields'][]=['id'=>HAmoCRM::getFieldId('email'), 'values'=>[['value'=>$email, 'enum'=>'WORK']]];
                }
                
                return static::newContact($contact);
            }
        }
        
        return null;
    }
    
    /**
     * Найти контакты
     * @param string $query строка для поиска
     * @param int $limit ограничение количества записей
     * @param int $offset сдвиг получения записей
     * @return []
     */
    public static function getContact($query, $limit=10, $offset=0)
    {
        if(mb_strlen($query) > 2) {
            return static::apiGet('api/v2/contacts', [
                'query'=>$query,
                'limit_rows'=>$limit,
                'limit_offset'=>$offset
            ]);
        }
        
        return null;
    }
    
    /**
     * Новый контакт
     * @link https://www.amocrm.ru/developers/content/api/contacts
     * @param [] $contacts параметры для запроса (секции "add").
     * Может быть передано несколько контактов.
     * @return string|[] идентификатор созданного контакта или набор идентификаторов,
     * если было передано несколько контактов.
     */
    public static function newContact($contacts)
    {
        $isOneContact=false;
        foreach($contacts as $key=>$value) {
            if(!is_numeric($key)) {
                $isOneContact=true;
                break;
            }
        }
        
        if($isOneContact) {
            $contacts=['add'=>[$contacts]];
        }
        else {
            $contacts=['add'=>$contacts];
        }
        
        foreach($contacts['add'] as $idx=>$contact) {
            $contacts['add'][$idx]['created_at']=time();
            $contacts['add'][$idx]['updated_at']=time();
            
            if(!empty($contact['custom_fields'])) {
                foreach($contact['custom_fields'] as $fieldIdx=>$field) {
                    if(empty($field['id'])) {
                        unset($contacts['add'][$idx]['custom_fields'][$fieldIdx]);
                    }
                }
            }
        }
        
        $result=static::apiPost('api/v2/contacts', $contacts);
        if(!empty($result['_embedded']['items'])) {
            if($isOneContact) {
                if(!empty($result['_embedded']['items'][0]['id'])) {
                    return $result['_embedded']['items'][0]['id'];
                }
            }
            else {
                return array_column($result['_embedded']['items'], 'id');                
            }
        }
        
        return null;
    }
    
    /**
     * Выполнить GET запрос к API AmoCRM.
     * @param string $method имя метода
     * @param [] $query дополнительные параметры для запроса
     * @return mixed|false
     */
    public static function apiGet($method, $query=[])
    {
        try {
            $provider=static::getProvider();
            
            $data=$provider->getHttpClient()->request('GET', $provider->urlAccount() . trim($method, '/'), [
                'headers'=>$provider->getHeaders(static::getAccessToken()),
                'query'=>$query
            ]);
            
            return json_decode($data->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Выполнить POST запрос к API AmoCRM.
     * @param string $method имя метода
     * @param [] $params дополнительные параметры для запроса
     * @return mixed|false
     */
    public static function apiPost($method, $params=[], $json=true)
    {
        try {
            $provider=static::getProvider();
            
            $data=$provider->getHttpClient()->request('POST', $provider->urlAccount() . trim($method, '/'), [
                'headers'=>$provider->getHeaders(static::getAccessToken()),
                ($json ? 'json' : 'form_params')=>$params
            ]);
            
            return json_decode($data->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return false;
        }
    }
}