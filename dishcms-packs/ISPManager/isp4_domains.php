<? 
/** 
 * Домены
 *
 * Массив доменов вида 
 * array(
 * 	username=>array(
 * 		'domain'=>имя основного домена
 * 		'disklimit'=>размер дисковой квоты (Мб). По умолчанию 500Мб.
 * 		'backup'=>путь к файлам бэкапа, относительно основной директории бэкапов "backupsPath"
 * 		'dumpfile'=>имя файла дампа базы данных, в директории дампов "sqlPath"
 * 	)
 * )
 */
 
return [
    'comfort154'=>['domain'=>'comfort154.ru', 'disklimit'=>500, 'dumpfile'=>'academy-comfort.sql', 'backup'=>'academy-comfort.ru/httpdocs']
];