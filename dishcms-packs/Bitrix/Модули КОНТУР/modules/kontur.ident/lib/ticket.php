<?php
namespace Kontur\Ident;

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TicketTable extends Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'kontur_ident_tickets';
    }

    /**
     * @link https://help.dent-it.ru/help/-struktura-peredavaemykh-obektov
     *
     * @return []
     */
    public static function getMap()
    {
        return [
            'ID' => new Main\Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true,
                'title' => 'Идентификатор записи'
            ]),
            
            // ! IDENT проверяет уникальность по полям уникального ключа. 
            // ! Поэтому вы не должны возвращать строки с совпадающими значениями в этом ключе. 
            // ! IDENT возьмет только одну из строк, какую именно — неизвестно.
            'TICKET_ID' => new Main\Entity\StringField('TICKET_ID', [
                'required' => true,
                'default_value' => function(){ return Helper::guid(); },
				'title' => 'Идентификатор заявки'
            ]),
            
            // ! IDENT проверяет уникальность по полям уникального ключа. 
            // ! Поэтому вы не должны возвращать строки с совпадающими значениями в этом ключе. 
            // ! IDENT возьмет только одну из строк, какую именно — неизвестно.
            'DATE_AND_TIME' => new Main\Entity\DatetimeField('DATE_AND_TIME', [
                'required' => true,
                'default_value' => function(){ return new Main\Type\DateTime(); },
                'title' => 'Дата создания заявки',
            ]),

            'FORM_NAME' => new Main\Entity\StringField('FORM_NAME', [
                'title' => 'Наименование формы, из которой была оставлена заявка'
            ]),

            // ! Телефон клиента может быть в любом формате, 
            // ! IDENT сам распознает корректные номера. Важно только, чтобы телефон был указан полностью — с кодом города, 
            // ! а также в значении присутствовал только номер (без лишнего текста) и он был единственным. 
            // ! Примеры корректных значений: +7 911 01 01 001, 7(777)0010101, (911) 0010101, 8-911-001-0101, 8812-0000-111. 
            // ! Единственный формат номеров, которые мы не распознаем, это локальные номера (8912..., 8964...), 
            // ! ошибочно начинающиеся с + (например, +89110001122), так как код +89 не принадлежит никакой стране.
            'CLIENT_PHONE' => new Main\Entity\StringField('CLIENT_PHONE', [
                'required' => true,
                'title' => 'Телефон клиента',
                'validation'=>function() {
                    return [
                        function($value) {
                            return !empty(preg_replace('/[^0-9]+/', '', $value));
                        }
                    ];
                },
                'save_data_modification' => function() {
                    return [
                        'sanitize'=>function($value, $fields) {
                            return trim(preg_replace(['/\s+/', '/[^0-9()\-\s]+/'], [' ', ''], $value));
                        }
                    ];
                }
            ]),

            'CLIENT_EMAIL' => new Main\Entity\StringField('CLIENT_EMAIL', [
                'title' => 'E-mail клиента',
                'validation'=>function() {
                    return [
                        function($value) {
                            return empty($value) || !!filter_var($value, FILTER_VALIDATE_EMAIL);
                        }
                    ];
                }
            ]),

            // ! если заполнено это поле, то поля ClientSurname, ClientName, ClientPatronymic должны 
            // ! отсутствовать либо быть пустыми
            'CLIENT_FULLNAME' => new Main\Entity\StringField('CLIENT_FULLNAME', [
                'title' => 'ФИО клиента'
            ]),

            // ! если заполнено это поле, то поле ClientFullName должно отсутствовать либо быть пустым
            'CLIENT_SURNAME' => new Main\Entity\StringField('CLIENT_SURNAME', [
                'title' => 'Фамилия клиента'
            ]),
            
            // ! если заполнено это поле, то поле ClientFullName должно отсутствовать либо быть пустым
            'CLIENT_NAME' => new Main\Entity\StringField('CLIENT_NAME', [
                'title' => 'Имя клиента'
            ]),

            // ! если заполнено это поле, то поле ClientFullName должно отсутствовать либо быть пустым
            'CLIENT_PATRONYMIC' => new Main\Entity\StringField('CLIENT_PATRONYMIC', [
                'title' => 'Отчество клиента'
            ]),
            
            // ! номер из IDENT, можно увидеть, если в карточке сотрудника подвести мышью на ФИО в заголовке
            'DOCTOR_ID' => new Main\Entity\IntegerField('DOCTOR_ID', [
                'title' => 'Id специалиста, к которому хотят записаться'
            ]),
            
            'DOCTOR_NAME' => new Main\Entity\StringField('DOCTOR_NAME', [
                'title' => 'ФИО специалиста, к которому хотят записаться.'
            ]),

            // ! не должно быть позже PlanEnd
            'PLAN_START' => new Main\Entity\DatetimeField('PLAN_START', [
                'title' => 'Желаемое время начала приема',
            ]),

            // ! не должно быть раньше PlanStart, а продолжительность приема не должна превышать 12 часов
            'PLAN_END' => new Main\Entity\DatetimeField('PLAN_END', [
                'title' => 'Желаемое время окончания приема',
            ]),

            'COMMENT' => new Main\Entity\TextField('COMMENT', [
                'title' => 'Комментарий',
            ]),

            'HTTP_REFERER' => new Main\Entity\TextField('HTTP_REFERER', [
                'default_value' => function(){ return preg_replace('/\?.*$/', '', $_SERVER['HTTP_REFERER']??''); },
                'title' => 'URL, с которого пришел запрос на сайт клиники',
            ]),

            'IDENT_EXCHANGE_STATUS' => new Main\Entity\IntegerField('IDENT_EXCHANGE_STATUS', [
                'default_value' => function(){ return Helper::STATUS_TICKET_NEW; },
                'title' => 'Статус'
            ]),
        ];
    }
}