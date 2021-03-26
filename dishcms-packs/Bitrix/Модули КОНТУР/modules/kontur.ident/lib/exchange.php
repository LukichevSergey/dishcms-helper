<?php
namespace Kontur\Ident;

use Bitrix\Main\Context;

/**
 * Класс обмена с IDENT
 * 
 * @link https://help.dent-it.ru/help/-variant-s-http
 */
class Exchange
{
    /**
     * Данные для формирования ответа
     *
     * @var []
     */
    private $responseData=[];

    /**
     * Запуск метода обмена
     *
     * @param string $action имя метода обмена
     * @return void
     */
    public static function run($action)
    {
        if(static::isAllowAction($action)) {
            $request=Context::getCurrent()->getRequest();
            $exchange=new static;

            switch(strtolower($action)) {
                case 'gettickets':
                    $exchange->actionGetTickets(
                        $request->get('dateTimeFrom'), 
                        $request->get('dateTimeTo'), 
                        $request->get('limit'), 
                        $request->get('offset') 
                    );
                break;
            }

            $exchange->send();
        }

        static::e403();
    }

    /**
     * Действие разрешено
     *
     * @param string $action имя действия
     * @return boolean
     */
    public static function isAllowAction($action)
    {
        if(in_array(strtolower($action), ['gettickets'])) {
            return static::checkAuth();
        }

        return false;
    }

    /**
     * Выдать заголовок "Доступ запрещен"
     *
     * @return void
     */
    public static function e403()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();        
        header('HTTP/1.1 403 Forbidden');
        exit;
    }

    /**
     * Возвращает ключ авторизации заданный в настройках модуля.
     *
     * @return string
     */
    public static function getAuthKey()
    {
        return trim(Helper::option('auth_key'))?:'';
    }

    /**
     * Проверяет авторизацию для обмена
     *
     * @return bool
     */
    public static function checkAuth()
    {
        return static::getAuthKey() === (apache_request_headers()['IDENT-Integration-Key']??null);
    }

    public function getResponseData()
    {
        return $this->responseData;
    }

    /**
     * Добавить данные для ответа
     *
     * @param mixed $data данные
     * @return void
     */
    public function addResponseData($data)
    {
        $this->responseData[]=$data;
    }

    /**
     * Отправить ответ
     *
     * @return void
     */
    public function send()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();
        echo json_encode($this->getResponseData(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Получить имя поля в IDENT для заявки
     *
     * @param string $name имя поля в TicketTable.
     * @return string|null если соответствия не найдено, 
     * то будет возвращено null.
     */
    public function getIdentTicketField($name)
    {
        /** @var [] $columns массив соответствия вида [имя_поля_в_TicketTable => имя_поля_в_IDENT] */
        $columns=[
            'TICKET_ID' => 'Id',
            'DATE_AND_TIME' => 'DateAndTime',
            'CLIENT_PHONE' => 'ClientPhone',
            'CLIENT_EMAIL' => 'ClientEmail',
            'FORM_NAME' => 'FormName',
            'CLIENT_FULLNAME' => 'ClientFullName',
            'CLIENT_SURNAME' => 'ClientSurname',
            'CLIENT_NAME' => 'ClientName',
            'CLIENT_PATRONYMIC' => 'ClientPatronymic',
            'DOCTOR_ID' => 'DoctorId',
            'DOCTOR_NAME' => 'DoctorName',
            'PLAN_START' => 'PlanStart',
            'PLAN_END' => 'PlanEnd',
            'COMMENT' => 'Comment',
            'HTTP_REFERER' => 'HttpReferer'
        ];

        return $columns[$name] ?? null;
    }

    /**
     * Загрузка заявок
     * 
     * Метод: GetTickets
     * 
     * @link https://help.dent-it.ru/help/-variant-s-http
     * 
     * Параметры:
     * dateTimeFrom — дата начала периода загрузки
     * dateTimeTo — дата окончания периода загрузки
     * limit, offset — необязательные параметры для итерационной загрузки
     *
     * @return void
     */
    public function actionGetTickets($dateTimeFrom=null, $dateTimeTo=null, $limit=0, $offset=0)
    {
        $filter=[];
        if($dateTimeFrom && strtotime($dateTimeFrom)) {
            $filter['>=DATE_AND_TIME']=date('d.m.Y H:i:s', strtotime($dateTimeFrom));
        }
        if($dateTimeTo && strtotime($dateTimeTo)) {
            $filter['<=DATE_AND_TIME']=date('d.m.Y H:i:s', strtotime($dateTimeTo));
        }

        $rs=TicketTable::getList([
            'filter'=>$filter,
            'order'=>['DATE_AND_TIME'=>'DESC'],
            'limit'=>(int)$limit,
            'offset'=>(int)$offset,
        ]);

        $map=TicketTable::getMap();
        while($ticket=$rs->fetch()) {
            $hasError=false;
            foreach($map as $name=>$field) {
                if($field->isRequired() && empty($ticket[$name])) {
                    $hasError=true;
                    break;
                }
            }

            if($hasError) {
                continue;
            }

            if(!empty($ticket['CLIENT_FULLNAME'])) {
                unset($ticket['CLIENT_NAME']);
                unset($ticket['CLIENT_SURNAME']);
                unset($ticket['CLIENT_PATRONYMIC']);
            }
            elseif(!empty($ticket['CLIENT_NAME']) && !empty($ticket['CLIENT_SURNAME']) && !empty($ticket['CLIENT_PATRONYMIC'])) {
                unset($ticket['CLIENT_FULLNAME']);
            }
            else {
                $hasError=true;
                continue;
            }

            $data=[];
            foreach($ticket as $field=>$value) {
                if(empty($value)) continue;
                if(($field == 'DATE_AND_TIME') && strtotime($value)) {
                    $value=date('c', strtotime($value));
                }
                if($identField=$this->getIdentTicketField($field)) {
                    $data[$identField]=$value;
                }
            }

            if(!empty($data)) {
                $this->addResponseData($data);
            }
        }
    }
}