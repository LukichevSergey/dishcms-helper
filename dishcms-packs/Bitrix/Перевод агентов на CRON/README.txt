https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2943

1) Для начала полностью отключим выполнение агентов на хите. Для этого выполним следующую команду в php консоли:

COption::SetOptionString("main", "agents_use_crontab", "N"); 
echo COption::GetOptionString("main", "agents_use_crontab", "N"); 

COption::SetOptionString("main", "check_agents", "N"); 
echo COption::GetOptionString("main", "check_agents", "Y");

В результате выполнения должно быть "NN".

2) После этого убираем из файла /bitrix/php_interface/dbconn.php определение следующих констант:

define("BX_CRONTAB_SUPPORT", true);
define("BX_CRONTAB", true);

И добавляем в этот файл:

if(!(defined("CHK_EVENT") && CHK_EVENT===true))
   define("BX_CRONTAB_SUPPORT", true);

3) копируем скрипт /bitrix/php_interface/cron_events.php:

4) И добавляем данный скрипт в cron:

Выполнение агентов на CRON

*/1 * * * * /usr/bin/php -f /home/bitrix/www/bitrix/php_interface/cron_events.php