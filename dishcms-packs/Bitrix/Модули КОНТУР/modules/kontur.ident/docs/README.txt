Примеры создания заявок для отправки в IDENT

// IDENT integration
if(\Bitrix\Main\Loader::includeModule('kontur.ident')) {
    \Kontur\Ident\Helper::createTicket([
        'FORM_NAME'=>$arParams['IDENT_FORM_NAME']??'',
        'CLIENT_FULLNAME'=>$_POST["user_name"]??'не указано',
        'CLIENT_PHONE'=>$_POST["user_tel"]??'',
        'CLIENT_EMAIL'=>$_POST["user_email"]??'',
        'COMMENT'=>implode(
            "\n" . str_repeat('-', 50) . "\n", 
            array_filter([$_POST["MESSAGE"]??'', $_POST["MESSAGE_2"]??'', $_POST["MESSAGE_3"]??''], function($value) {
                return !!trim($value);
            })
        )
    ]);
}