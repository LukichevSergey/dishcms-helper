<?
/**
 * ISP Manager 4 Миграция
 * 
 * @link http://geograph.us/massovoe-dobavlenie-domenov-ispmanager-5.html
 * 
 * @todo на данный момент, жестко задан шаблон пути к файлам домена 
 * пользователя в методе ISPManager::restore()
 */
date_default_timezone_set('Asia/Novosibirsk');
set_time_limit(0);
error_reporting(E_ALL);

$isp=new ISPManager([
	'mgrctl'=>'/usr/local/ispmgr/sbin/mgrctl',
	'url'=>'https://x.x.x.x:1500/ispmgr',
	'login'=>'',
	'pwd'=>'',
	'owner'=>'root',
	'ip'=>'x.x.x.x',
	'domains'=>dirname(__FILE__).'/isp4_domains.php',
	'backupsPath'=>'/home/backups/files',
	'sqlPath'=>'/home/backups/mysqldumps/sql',
	'wwwPath'=>'/var/www',
	'log'=>dirname(__FILE__).'/isp4_domains54.log'
]);

$isp->run();

/**
 * ISPManager class
 * 
 */
class ISPManager 
{	
	/**
	 * 
	 * @var array массив настроек
	 */
	private $_options=[];
	
    /**
     * @var resource дескриптор cURL.
     */
    private $_ch;
    
    /**
     * @var array массив логов.
     */
    private $logs=[];
    
    /**
     * @var boolean были записаны данные в лог файл.
     */
    private $logged=false;
    
    /**
     * Конструктор
     * @param array $options параметры инициализации.
     * 'url'=>'https://127.0.0.1:1500/ispmgr' - ссылка на ISP Manager;
	 * 'login'=>'root' - логин;
	 * 'pwd'=>'' - пароль;
	 * 'owner'=>'root' - владелец пользователя;
	 * 'ip'=>'127.0.0.1' - ip адрес домена;
	 * 'domains'=>dirname(__FILE__).'/isp4_domains54.php' - файл с конфигурацией пользователей и доменов;
	 * 'backupsPath'=>'/home/backups/files' - путь к файлам резервных копий доменов
	 * 'sqlPath'=>'/home/backups/sql' - путь к файлам резервных копий баз данных
	 * 'wwwPath'=>'/var/www' - путь к директориям пользователей
	 * 'log'=>dirname(__FILE__).'/isp4_domains54.log' - имя файла системного лога
     */
    public function __construct($options) 
    {
        $this->_options=$options;
    }
    
    /**
     * Запуск процесса миграции.
     */
    public function run()
    {
    	$domains=$this->getDomains();
    	foreach($domains as $username=>$config) {
    		if(strlen($username) > 16) {
    			$username=substr($username, 0, 16);
    		}
    		$this->addUser($username, $this->get($config, 'domain'), $this->get($config, 'disklimit', 500));
    		$this->restore($username, $this->get($config, 'domain'), $this->get($config, 'backup'));
    		$this->addDb($username, $this->get($config, 'dumpfile'));
    		$this->log("\r\n=====================================================================\r\n\r\n");
    	}
    	$this->printLogs();
    }
    
    /**
     * Получить данные о доменах.
     * @throws \Exception
     * @return array массив данных о домене в формате
     * array(
     * 	username=>array(
     * 		'domain'=>имя основного домена
     * 		'disklimit'=>размер дисковой квоты (Мб). По умолчанию 500Мб.
     * 		'backup'=>путь к файлам бэкапа, относительно основной директории бэкапов "backupsPath"
     * 		'dumpfile'=>имя файла дампа базы данных, в директории дампов "sqlPath"
     * 	)
     * )
     */
    protected function getDomains()
    {
    	if(!is_file($this->oget('domains'))) {
    		throw new \Exception('Файл не найден: ' . $this->oget('domains'));
    	}
    	
    	$domains=include($this->oget('domains'));
    	if(!is_array($domains)) {
    		$domains=[];
    	}
    	
    	return $domains;
    }
    
    /**
     * Добавить данные в лог файл.
     * @param string|array $data данные
     * @param string $title заголовок
     */
    protected function log($data, $title='')
    {
    	if(is_array($data)) {
    		$data=implode("\r\n", $data);
    	}
    	    	
    	$content='';
    	if(!$this->logged) {
    		$content="\r\n-----------------------------------------------------------------\r\n" 
    			. date("Y.m.d H:i:s")
    		. "\r\n-----------------------------------------------------------------\r\n";
    		$this->logged=true;
    	}
    	
    	if($title) {
    		$content.=$title."\r\n";
    	}
    	
    	$content.=$data."\r\n";
    	
    	file_put_contents($this->oget('log'), $content, FILE_APPEND);
    }
    
    /**
     * Вывести на экран основной лог приложения.
     */
    protected function printLogs()
    {
    	$this->log("\r\n\r\n\r\n=====================================================================\r\n\r\n");
    	$this->log($this->logs);
    	$this->log("\r\n\r\n=====================================================================\r\n\r\n\r\n");
    	
    	echo implode("\r\n", $this->logs);
    }
    
    /**
     * Получить значение элемента из массива
     * @param array $array массив.
     * @param string $key ключ.
     * @param mixed $default значение по умолчанию. 
     */
    protected function get($array, $key, $default=null)
    {
    	if(array_key_exists($key, $array)) {
    		return $array[$key];
    	}
    	return $default;
    }
    
    /**
     * Получить значение параметра из массива настроек
     * @param string $name имя параметра 
     * @param mixed $default значение по умолчанию. 
     */
    protected function oget($name, $default=null) 
    {
    	return $this->get($this->_options, $name, $default);
    }    
    
    /**
	 * Выполнить запрос
	 * @param array $params параметры запроса.
	 * @param string $logLabel метка лога.
     */
    protected function exec($params, $logLabel='')
    {
    	$ch=curl_init();
		curl_setopt_array($ch, [
		    CURLOPT_RETURNTRANSFER=>true, 
		    CURLOPT_SSL_VERIFYHOST => false, 
		    CURLOPT_SSL_VERIFYPEER=>false
		]);
		
    	$url=$this->oget('url') . '?authinfo=' . urlencode($this->oget('login')) . ':' . urlencode($this->oget('pwd'));
 		$url.= '&' . http_build_query($params);
    	
    	curl_setopt($ch, CURLOPT_URL, $url);
    	
    	$response=(array)json_decode(curl_exec($ch), true);
    	
    	curl_close($ch);
    	
    	$result = 'ERROR';    	
    	if(isset($response['error'])) {
    		$result=$response['error']['msg'];
    	}
    	elseif((isset($response['result']) && $response['result'] == 'OK') || isset($response['ok'])) {
    		$result='OK';
    	}
    	
    	$this->logs[]="{$logLabel}\t{$result}";
    }
    
    /**
     * Добавление нового пользователя и домена
     * @param string $username имя пользователя
     * @param string $domain имя пользователя
     * @param string $disklimit размер дисковой квоты (Мб)
     */
    protected function addUser($username, $domain, $disklimit=500)
    {
    	$pwd=$this->generatePwd();
    	$this->exec([
   			'name'=>$username,
   			'passwdgen'=>$pwd,
   			'passwd'=>$pwd,
   			'confirmgen'=>$pwd,
   			'confirm'=>$pwd,
   			'owner'=>$this->oget('owner'),
   			'ip'=>$this->oget('ip'),
   			'domain'=>$domain,
   			'preset'=>'dishman',
   			'email'=>'',
   			'cgi'=>'on',
   			'phpmod'=>'on',
   			'phpcgi'=>'on',
   			'phpfcgi'=>'on',
   			'disklimit'=>$disklimit,
   			'ftplimit'=>2,
   			'maillimit'=>0,
   			'webdomainlimit'=>5,
   			'maildomainlimit'=>0,
   			'baselimit'=>2,
   			'baseuserlimit'=>2,
   			'bandwidthlimit'=>10000,
   			'cpulimit'=>10,
   			'memlimit'=>64,
   			'proclimit'=>10,
   			'mysqlquerieslimit'=>0,
   			'mysqlupdateslimit'=>0,
   			'mysqlconnectlimit'=>0,
   			'mysqluserconnectlimit'=>0,
   			'limitconn'=>0,
   			'mailrate'=>100,
   			'note'=>$pwd,
   			'func'=>'user.edit',
   			'elid'=>'',
   			'sok'=>'ok'
		], "Добавление пользователя {$username}:{$pwd}");
    }
    
    /**
     * Добавление нового пользователя и домена
     * @param string $username имя пользователя
     * @param string $dumpfile путь к файлу с дампом базы
     */
    protected function addDb($username, $dumpfile)
    {
    	if(strlen($username) > 13) {
    		$dbname=substr($username, 0, 13).'_db';
    	}
    	else {
    		$dbname=$username.'_db';
    	}
    	
    	$pwd=$this->generatePwd();    	
    	/*$this->exec([
    		'name'=>$dbname,
    		'dbtype'=>'MySQL',
    		'owner'=>$username,
    		'dbencoding'=>'utf8',
    		'dbuser'=>'newuser',
    		'dbusername'=>$dbname,
    		'dbpasswordgen'=>$pwd,
    		'dbpassword'=>$pwd,
    		'dbconfirmgen'=>$pwd,
    		'dbconfirm'=>$pwd,
    		'func'=>'db.edit',
    		'elid'=>'',
    		'sok'=>'ok'
    	], "Создание базы данных {$dbname}:{$pwd}");
    	*/
    	
    	$params=[
    		'name'=>$dbname,
    		'owner'=>$username,
    		'dbusername'=>$dbname,
    		'dbpassword'=>$pwd,
    		'dbencoding'=>'utf8',
    		'dbtype'=>'MySQL',
    		'sok'=>'ok'
    	];
    	 
    	// создание базы данных
    	$this->execMgrctl('db.edit', $params, "Создание базы данных {$dbname}:{$pwd}");
    	
    	// импорт данных
    	$output=null;
    	$sqlFile=$this->oget('sqlPath')."/{$dumpfile}";
    	exec("mysql -u{$dbname} -p{$pwd} {$dbname} < {$sqlFile}", $output);
    	$this->log($output, "Импорт данных в {$username}:{$dbname} из файла {$sqlFile}");
    }
    
    /**
     * Восстановление файлов из бэкапа.
     * @param string $username имя пользователя
     * @param string $domain имя домена
     * @param string $path путь к файлам бэкапа относительно директории бэкапов.
     */
    protected function restore($username, $domain, $path)
    {
    	$backupPath=$this->oget('backupsPath') . "/{$path}";
    	$wwwPath=$this->oget('wwwPath') . "/{$username}/data/www/{$domain}";
    	
    	// удаление файла index.html
    	$output=null;
    	exec('rm '.$wwwPath.'/index.html', $output);
    	$this->log($output, "Удаление файла index.html {$wwwPath}/index.html");
    	
    	// копирование файлов
    	$output=null;
    	exec('find '.$backupPath.'/* -exec mv {} '.$wwwPath.'/ \;', $output);
    	$this->log($output, "Копирование файлов из {$backupPath} в {$wwwPath}");
    	
    	// копирование файлов начинающихся с точки
    	$output=null;
    	exec('find '.$backupPath.'/ -type f -name \'.*\' -exec mv {} '.$wwwPath.'/ \;', $output);
    	$this->log($output, "Копирование файлов начинающихся с точки из {$backupPath} в {$wwwPath}");
    	
    	// установка прав файлов
    	$output=null;
    	exec("chown -R {$username}:{$username} {$wwwPath}/*", $output);
    	$this->log($output, "Установка прав на файлы {$wwwPath}/*");
    	$output=null;
    	exec("chown -R {$username}:{$username} {$wwwPath}/.*", $output);
    	$this->log($output, "Установка прав на файлы {$wwwPath}/.*");    	
    }
    
    /**
     * Выполение команды ispctl
     * @param string $command имя команды
     * @param array $params массив параметров
     * @param string $logTitle заголовок лога.
     */
    protected function execMgrctl($command, $params, $logTitle='')
    {
    	$output=null;
    	exec($this->oget('mgrctl') .' -m ispmgr '.$command.$this->paramsToString($params));
    	$this->log($output, $logTitle);
    }
    
    /**
     * Конвертация параметров в строку
     * @param array $params массив параметров
     * @return string
     */
    protected function paramsToString($params)
    {
    	$result='';
    	foreach($params as $name=>$value) {
    		$result.=' '.$name.'='.$value;
    	}
    	return $result;
    }
    
    /**
     * Генерация пароля
     * @link http://www.php.su/articles/?cat=examples&page=021
     * @param integer $max длина пароля
     * @return string
     */
    protected function generatePwd($max=8)
    {
    	$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
    	$size=strlen($chars)-1;
    	$pwd='';
    	while($max--) {
    		$pwd.=$chars[rand(0,$size)];
    	}
    	return $pwd;
    }
}