<?php
/**
 * Скрипт получения файла данных с веб-сервера 1с
 * @version 1.1 
 */
define('KONTUR_1C_LOG_MODE', 0);

$config=[
	// path (string) папка импорта, которая указана в настройках 1С-Битрикс
	'path'=>$_SERVER['DOCUMENT_ROOT'].'/upload/import_csv', 
	// разрешенные имена файлов
	'allow_files'=>['import.csv', 'stock.csv'],
	// настройки OpenSSL кодирования 
    'openssl'=>[
        'method'=>'AES-192-CBC',
        'pwd'=>':!JCQ6vk/HQ~J&wRv5u\'C&#'
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
	
	public static function read($filename)
	{
		$filename=self::createPath($filename);
		if(is_file($filename))
			return file_get_contents($filename);
		
		return false;
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
            throw new \CException('Missing "' . $name . '" option.');

        return $empty ? null : $config[$name];
    }
}

class Kontur_Listener
{
	public static function run(\Kontur_Config $config)
	{ 
		try {
			if(empty($_POST['hash']) || empty($_POST['filename']) || empty($_POST['content']) || empty($_POST['iv']))
				throw new \Exception('Invalid params.');

			$filename=$_POST['filename'];
			Kontur_Log::log('Filename: '.$filename);

			$allowFiles=$config->get('allow_files');
			if(!empty($allowFiles) && (!is_array($allowFiles) || !in_array($filename, $allowFiles)))
				throw new \Exception('File "'.$filename.'" not allowed');

			$hash=$_POST['hash'];
			$fileContent=$_POST['content'];

			$openSSLiv=$_POST['iv'];
			$openSSL=$config->get('openssl', true);
			$fileContent=openssl_decrypt(
				$fileContent,
				$config->get('method', true, $openSSL),
				$config->get('pwd', true, $openSSL),
				OPENSSL_RAW_DATA,
				$openSSLiv
			);

			$hashContent=Kontur_File::hashContent($fileContent);
			if($hash !== $hashContent)
				throw new \Exception('Ivalid hash file');

			$lastFileContent=Kontur_File::read([$config->get('path'), $filename]);
			$hashLastContent=Kontur_File::hashContent($lastFileContent);
			if(($importFile === false) || ($hashContent !== $hashLastContent)) {
				Kontur_Log::log('Update: '.$filename);
				Kontur_File::write([$config->get('path'), $filename], $fileContent);
				return true;
			}
		}
		catch(\Exception $e) {
			Kontur_Log::log($e->getMessage());
		}

		return false;
	}
}

Kontur_Log::log('Start session.');
Kontur_Listener::run(Kontur_Config::create($config));
Kontur_Log::log('End session.');
?>
