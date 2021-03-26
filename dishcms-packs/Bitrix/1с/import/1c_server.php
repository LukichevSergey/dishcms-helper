<?php
/**
 * Скрипт передачи файлов с веб-сервера 1С на сервер сайта 1С-Битриск
 * @version 1.1 
 */
define('KONTUR_1C_LOG_MODE', 0);

$config=[
	// url (string) ссылка на скрипт слушателя
	'url'=>'http://tdautoram.konturdev.tmweb.ru/1c_import/1c_bx_listener.php',
	// auth (string) строка для HTTP AUTH BASIC авторизации формата "user:password"
    'auth'=>'1cbxsrv:g9N275vNDLy3Te8L',
    // настройки OpenSSL кодирования 
    'openssl'=>[
        'method'=>'AES-192-CBC',
        'pwd'=>':!JCQ6vk/HQ~J&wRv5u\'C&#'
    ],
    // список файлов обмена
    // Прим.: если один из файлов найден не будет, 
    // процесс будет продолжен для остальных файлов.
	'files'=>[
		[
			// filepath (string) путь к папке файла
			'filepath'=>'/home/k/konturdev/1c.konturdev.tmweb.ru/1c_import',
			// filename (string) имя файла
			'filename'=>'import.csv',
			// iconv (array) (необязательный) параметры для конвертирования файла 
			// в другую кодировку. Параметры [из, в].
			'iconv'=>['CP1251', 'UTF-8']
		],
		[
			'filepath'=>'/home/k/konturdev/1c.konturdev.tmweb.ru/1c_import',
            'filename'=>'stock.csv'
        ]
	]
];

class Kontur_Log
{
	public static function log($msg)
	{
		if(!KONTUR_1C_LOG_MODE) 
			return true;

		Kontur_File::write(
           [__DIR__, 'logs', 'log_'.date('Y_m_d').'.log'],
           date('d.m.Y H:i:s: ') . $msg . "\n",
           'a+'
        );
	}
}
class Kontur_File 
{
	public static function write($filename, $data, $mode='w+', $dirMode=0700)
	{
		$filename=self::createPath($filename);
		
		if(!is_dir(dirname($filename)))
			mkdir(dirname($filename), $dirMode, true);
		
		$fp = fopen($filename, $mode);
		fwrite($fp, $data);
		fclose($fp);
	}
	
	public static function read($filename, $returnArray=false)
	{
		$filename=self::createPath($filename);
		if(is_file($filename))
			return $returnArray ? file($filename) : file_get_contents($filename);
		
		return false;
	}

	public static function rm($filename)
	{
		$filename=self::createPath($filename);
		if(is_file($filename))
			unlink($filename);
	}
	
	public static function createPath($path)
	{
		if(is_array($path)) {
			return implode(DIRECTORY_SEPARATOR, array_filter($path, function($path) {
				return (!empty($path) || is_numeric($path));
			}));
		}
		
		return realpath($path);
	}
	
	public static function hashContent($content)
	{
		return sha1($content);
	}
}
class Kontur_Config
{
	protected $config=[];

	public function __construct($config)
	{
		$this->config=$config;
	}

	public static function create($config)
	{
		return new self($config);
	}

	public function get($name, $required=false, $config=null)
    {
		if($config === null)
			$config=$this->config;

        $empty=empty($config[$name]);
        if($required && $empty)
            throw new \Exception('Missing "' . $name . '" option.');

        return $empty ? null : $config[$name];
    }
}

class Kontur_CURL
{
	public static function curl($options)
    {
        $ch=curl_init();
        curl_setopt_array($ch, $options);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

	public static function sendFiles(\Kontur_Config $config)
	{
		try {
			$result=[];
			$files=$config->get('files', true);
			if(is_array($files)) {
				foreach($files as $fileConfig) {
					$filePath=$config->get('filepath', true, $fileConfig);
					$fileName=$config->get('filename', true, $fileConfig);
					$fileContent=Kontur_File::read([$filePath, $fileName]);
					if(!empty($fileContent)) {
						$iconv=$config->get('iconv', false, $fileConfig);
						if(is_array($iconv) && (count($iconv)==2)) {
							$fileContent=iconv($iconv[0], $iconv[1], $fileContent);
						}

						$fileHash=Kontur_File::hashContent($fileContent);

						$openSSLiv=openssl_random_pseudo_bytes(16);
						$openSSL=$config->get('openssl', true);
						$fileContent=openssl_encrypt(
							$fileContent,
							$config->get('method', true, $openSSL),
							$config->get('pwd', true, $openSSL),
							OPENSSL_RAW_DATA,
							$openSSLiv
						);

						$result[]=self::curl([
							CURLOPT_POST=>1,
							CURLOPT_ENCODING=>"gzip", 
							CURLOPT_URL=>$config->get('url', true),
							CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
							CURLOPT_USERPWD=>$config->get('auth', true),
							CURLOPT_POSTFIELDS=>[
								'iv'=>$openSSLiv,
								'hash'=>$fileHash,
								'filename'=>$fileName,
								'content'=>$fileContent
							]
						]);
					}
					Kontur_File::rm([$filePath, $fileName]);
				}
			}
			return $result;
		}
		catch(\Exception $e) {
			Kontur_Log::log($e->getMessage());
		}

		return false;
	}
}

Kontur_Log::log(date('Y.m.d H:i:s').'[start session]');
$result=Kontur_CURL::sendFiles(Kontur_Config::create($config));
if($result !== false) {
	// Kontur_Log::log(print_r($result, true));
}
Kontur_Log::log(date('Y.m.d H:i:s').'[end session]');
echo 'done';