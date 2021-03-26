<?php
$MESS["NEW_TICKETS_TAB_TITLE"]="Новые заявки";
// $MESS["NEW_TICKETS_NOTE"]='Здесь отображаются заявки, которые еще не были отправлены в IDENT.';
$MESS["NEW_TICKETS_NOTE"]='1) Здесь отображаются все заявки, которые будут отправлены в IDENT.
<br/>2) Если заявка уже была ранее отправлена в IDENT, при последующей выгрузке дублироваться в IDENT она уже не будет.
<br/>3) Заявки для выгрузки храняться на сайте 3 дня. 
<br/>4) Проверка удаления заявок происходит запуском агента <code>\Kontur\Ident\Agent\DeleteOldTickets</code> в интервале заданном на странице
&laquo;<a class="adm-info-message-title" href="/bitrix/admin/agent_list.php?lang=ru">Агенты</a>&raquo;';
$MESS["DONE_TICKETS_TAB_TITLE"]="Отправленыые заявки";
$MESS["DONE_TICKETS_NOTE"]='Здесь отображаются заявки, которые уже были отправлены в IDENT, но которые еще ожидают запуска агента удаления.';
$MESS["TICKETS_TABLE_HEADER_DATE"]='Дата';
$MESS["TICKETS_TABLE_HEADER_INFO"]='Заявка';
$MESS["TICKETS_TABLE_EMPTY"]='Заявок нет';