<?php
/**
 * Сканер вредоносного кода.
 * 
 * @copyright 2018 KONTUR (kontur-lite.ru)
 */
ini_set('default_charset', 'utf8');
mb_internal_encoding('utf-8');

KScan::run();

class KScan
{
	const RETURN_FILES=1;
	const RETURN_DIRS=2;
	
	const FILELIST_FILENAME='kscan_filelist.log';
	const DIRLIST_FILENAME='kscan_dirlist.log';
	const CONFIG_FILENAME='kscan_config.ini';
	const LOG_FILENAME='kscan.log';
	const VIRUS_FILENAME='kscan_virused.log';
	const VERIFED_FILENAME='kscan_verifed.data';
	
	const STEP_START=0;
	const STEP_GET_FILE_LIST=10;
	const STEP_SCANING=100;
	const STEP_DONE=200;
	
	const READ_FILESIZE=1048576;
	const READ_MAXSIZE=10485760;
	
	// @var string имя файла списка файлов
	private $filelist;
	
	// @var string имя файла списка директорий
	private $dirlist;
	
	// @var string имя файла конфигурации
	private $configfile;
	
	// @var string имя файла лога
	private $logfile;
	
	// @var string имя файла лога найденых зараженных файлов
	private $virusfile;
	
	// @var string имя файла доверенных файлов (исключения)
	private $verifedfile;
	
	// @var string путь к корневой директории с которой начинается поиск
	private $root;
	
	// @var array конфигурация
	private $config=[];
	
	// @var integer кол-во обработанных файлов/директорий
	private $cnt=0;
	
	// @var integer максимальное кол-во обработываемых файлов/директорий
	private $limit=500;
	
	// @var integer максимальное кол-во обработываемых файлов/директорий
	private $scanlimit=250;
	
	// @var integer текущий шаг
	private $step=null;
	
	// @var integer кол-во символов радиуса охвата части кода с вредоносным фрагментом для отчета.
	private $coderange=400;
	
	// @var array кэш
	private static $cache=[
		'filelist_count'=>0, // кол-во файлов в списке файлов для сканирования
		'virus_founds'=>[], // найденые файлы, содержащие вредоносный код
	];
	
	// @var array кэш уже существующих файлов.
	private static $ecache=[];
	
	// @var array кэш уже полученных директорий.
	private static $edcache=[];
	
	public static function run($path=false)
	{
		if(!$path) {
			$path=dirname(__FILE__);
		}
		
		$scan=new static;
		$scan->root=$scan->npath($path);
		$scan->filelist=$scan->root . DIRECTORY_SEPARATOR . self::FILELIST_FILENAME;
		$scan->dirlist=$scan->root . DIRECTORY_SEPARATOR . self::DIRLIST_FILENAME;
		$scan->configfile=$scan->root . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME;
		$scan->logfile=$scan->root . DIRECTORY_SEPARATOR . self::LOG_FILENAME;
		$scan->virusfile=$scan->root . DIRECTORY_SEPARATOR . self::VIRUS_FILENAME;
		$scan->verifedfile=$scan->root . DIRECTORY_SEPARATOR . self::VERIFED_FILENAME;
		
		$scan->loadconfig();
		$scan->prepare();
	}
	
	/**
	 * Правила поиска вредоносного кода (простые, регистрозависимые)
	 * @return array
	 */
	private function rules()
	{
		return [
			'if(isset(${$',
			'@$overwrite',
			'@include',
			'get_magic_quotes_gpc',
			'shen',
			'test_url',
			'","","',
			'$includephpcode',
			'gzinflate',
			'){eval',
			'wp-login',
			'<?php @',
			'$_POST["mailto"]',
			'admins',
			'$USER->Authorize($_',
			'eval ($',
			'eval($',
			'eval ( $',
			'eval( $',
			'base64_decode',
			'$GLOBALS[\'__',
			'chr(117)',
            'chr(111)',
            'chr(110)',
            'assign'
		];
	}
	
	
	/**
	 * Правила поиска вредоносного кода (простые, регистронезависимые)
	 * @return array
	 */
	private function irules()
	{
		return [
		];
	}

	/**
	 * Правила поиска вредоносного кода (REGEXP)
	 * @return array
	 */
	private function rrules()
	{
		return [
            '/(\s*\.\s*\$[^[]*\[[^]]+\]){4,}/m'
		];
	}
	
	/**
	 * Последовательность шагов
	 * @return array
	 */
	private function steps()
	{
		return [
			self::STEP_START,
			self::STEP_GET_FILE_LIST,
			self::STEP_SCANING,
			self::STEP_DONE
		];
	}
	
	private function stepLabels($step=false)
	{
		$labels=[
			self::STEP_START=>'Запуск получения списка сканируемых файлов',
			self::STEP_GET_FILE_LIST=>'Получение списка сканируемых файлов',
			self::STEP_SCANING=>'Сканирование',
			self::STEP_DONE=>'Сканирование завершено'
		];
		
		return ($step===false) ? $labels : $labels[$step];
	}
	
	/**
	 * Нормализация пути
	 */
	private function npath($filename, $trim=1)
	{
		$path=preg_replace('#[/\\\\]+#', '/', $filename);
		if($trim===true) return trim($path, '/');
		elseif($trim===1) return rtrim($path, '/');
		elseif($trim===-1) return ltrim($path, '/');
		return $path;
	}
	
	private function prepare()
	{
		$this->step=(int)$this->cget('step', -1);
		if($this->step < 0) {
			$this->cset('step', self::STEP_START);
			$this->msg('<a href="?'.time().'">Запустить процесс сканирования</a>', false);
			return true;
		}
		
		if(!$this->step) {
			$this->step=self::STEP_START;
			$this->nextstep();
			return true;
		}
		
		if(isset($_GET['rescan'])) {
			$this->cset('step', self::STEP_SCANING);
			$this->cset('scan_pos', 0);
			$this->cset('virus_count', 0);
			$date=date('YmdHis');
			if(is_file($this->virusfile)) {
				copy($this->virusfile, $this->virusfile.'.bak'.$date);
				@unlink($this->virusfile);
			}
			if(is_file($this->getVirusFileListFilename())) {
				copy($this->getVirusFileListFilename(), $this->getVirusFileListFilename().'.bak'.$date);
				@unlink($this->getVirusFileListFilename());
			}
			$this->msg($msg, true);
		}
		elseif(isset($_GET['restart'])) {
			@unlink($this->configfile);
			@unlink($this->dirlist);
			@unlink($this->filelist);
			@unlink($this->virusfile);
			@unlink($this->getVirusFileListFilename());
			$this->msg($msg, true);
		}
		elseif(($this->step===self::STEP_GET_FILE_LIST) || !$this->cget('filelist_complite', 0)) {
			$this->prepareExistsDirInLog();
			$this->prepareExistsInLog();
			$last=$this->getlast();
			$isroot=false;
			if(!$last) {
				$last=$this->root;
				$isroot=true;
			}
			elseif(is_dir($last)) {
				$isroot=($last===$this->root);
			}
			elseif(is_file($last)) {
				$isroot=(dirname($last)===$this->root);
			}
			else {
				$this->nextstep('Путь для сканирования некорректен.', false);
				return false;
			}
			
			if($this->read($last, static::RETURN_FILES, true, $isroot)) {
				$this->cset('filelist_complite', 1);
				$this->nextstep('Процесс получения списка файлов для сканирования завершен.');
			}
			else {
				if(preg_match('#^'.$this->root.'(.*)$#', $last, $path)) {
					$paths=explode('/', $this->npath($path[1],true));
					while(array_pop($paths)) {
						$path=implode('/', $paths);
						$isroot=false; 
						if(!$path || ($path === $this->root)) {
							$path=$this->root;
							$isroot=true;
						}
						else $path=$this->root.'/'.$path;
						
						if($this->read($path, static::RETURN_FILES, true, $isroot)) {
							$this->cset('filelist_complite', 1);
							$this->nextstep('Процесс получения списка файлов для сканирования завершен.', true);
							return true;
						}
					}
				}

				$this->nextstep('<b>Последний файл/директория:</b> ' . $this->getlast($this->root), false);
			}
		}
		elseif(($this->step===self::STEP_SCANING) && $this->cget('filelist_complite', 0)) {
			$this->prepareExistsInLog($this->getVirusFileListFilename());
			$starttime=time();
			$scanpos=(int)$this->cget('scan_pos', 0);;
			$result=$this->scan();
			if(!empty(static::$cache['virus_founds'])) {
				$msg='<div style="color:#F44336"><b>Последние найденные подозрительные файлы:</b><br/>';
				foreach(static::$cache['virus_founds'] as $info) {
					$msg.=$info['filename'].'<br/>';
				}
				$msg.='</div>';
			}
			if($result===true) {
				$msg.='<br/><b style="color:#4CAF50">Скнирование заверешно.</b><br/><br/>';
				$this->nextstep($msg);
			}
			elseif($result===false) {
				$endtime=time();
				$msg.='<b>Просканировано:</b> ' . $scanpos + (int)$this->cget('scan_pos', 0) . ' из ' . static::$cache['filelist_count'] . '<br/>';
				$msg.='<b>Осталось до завершения:</b> ' 
					. ceil((($endtime-$starttime) * ceil(((int)static::$cache['filelist_count']-(int)$this->cget('scan_pos', 0))/$this->scanlimit))/60)
					. ' мин.<br/>';
				$msg.='<span style="color:#F44336"><b>Найдено подозрительных файлов:</b> ' . $this->cget('virus_count', 0) . '</span><br/>';
				$this->nextstep($msg, false);
			}
			else {
				$this->nextstep('Файл списка файлов для сканирования не найден, либо пуст.');
			}
		}
		else {
			if(isset($_GET['delete'])) {
				$this->remove();
			}
			elseif(isset($_GET['save'])) {
				$this->save();
			}
			elseif(isset($_GET['view'])) {
				$this->view($_GET['view']);
			}
			elseif(isset($_GET['edit'])) {
				$this->edit($_GET['edit']);
			}
			elseif(isset($_GET['controls'])) {
				$this->controls();
			}
			else {
				$msg.='<br/><b style="color:#4CAF50">Процесс сканирования завершен.</b><br/><br/>';
				$msg.='<a href="?rescan=1&'.time().'">Перезапустить процесс сканирования</a>';
				$msg.='&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="?restart=1&'.time().'">Перезапустить процесс поиска</a>';
				$msg.='<br/><br/><br/><a href="?controls=1&'.time().'">Перейти к управлению</a>';
				$this->msg($msg, false);
			}
		}
	}
	
	private function nextstep($msg='', $nextstep=true)
	{
		$msg='<b>Текущая операция:</b> ' . $this->stepLabels($this->step) . '<br/>'.$msg;
		
		if($nextstep && ($this->step!==self::STEP_DONE)) {
			$next=false;
			foreach($this->steps() as $step) {
				if($step === $this->step) $next=true;
				elseif($next) {
					$this->step=$step;
					$this->cset('step', $this->step);
					break;
				}
			}
		}

		switch($this->step) {
			case self::STEP_START:
				$msg.='<br/>Ожидайте завершения процесса...';
				$this->msg($msg, true);
				break;
			case self::STEP_GET_FILE_LIST:
				$lasts=$this->getlast([], true);
				if(count($lasts) > 1) {
					array_pop($lasts);
					$countfrom=static::$cache['filelist_count']+$this->limit;
					$counttotal=$this->getTotalFiles();
					if($countfrom > $counttotal) $countfrom=$counttotal;
					$msg.='<br/><b>Обработано:</b> ' . $countfrom . ' из ' . $counttotal . ' файлов';
					$msg.='<br/><b>Последние добавленные файлы:</b></br><small>';
					foreach($lasts as $last) {
						$msg.=$last.'</br>';
					}
					$msg.='</small>';
				}
				$msg.='<br/>Ожидайте завершения процесса...';
				
				$this->msg($msg, true);
				break;
			case self::STEP_SCANING:
				if($nextstep) {
					$this->cset('scan_pos', 0);
				}
				$msg.='<br/>Ожидайте завершения процесса...';
				$this->msg($msg, true);
				break;
			case self::STEP_DONE:
				if(is_file($this->virusfile)) {
					$msg.='<b>Найдено подозрительных файлов:</b> ' . $this->cget('virus_count', 0) . '<br/>';
					$msg.='Файл с отчетом сканирования находится здесь: ' . $this->virusfile . '<br/>';
					$msg.='Файл со списком файлов находится здесь: ' . $this->getVirusFileListFilename() . '<br/>';
				}
				$msg.='<br/><br/><a href="?rescan=1&'.time().'">Перезапустить процесс сканирования</a>';
				$msg.='&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="?restart=1&'.time().'">Перезапустить процесс поиска</a>';
				$msg.='<br/><br/><br/><a href="?controls=1&'.time().'">Перейти к управлению</a>';
				$this->msg($msg, false);
				break;
		}
		
		exit;
	}
	
	private function msg($msg, $reload=false)
	{
		static $head=false;
		if(!$head) {
			echo '<!DOCTYPE html><html><head><meta charset="utf-8" /><title>KONTUR-LITE VIRUS SCANNER</title></head><body>';
		}
		echo '<p>' . $msg . '</p>';
		if($reload===true) {
			$reload=preg_replace('#^([^?]+)\?(.*)$#', '\1', $_SERVER['REQUEST_URI']).'?'.time();
			//echo '<script>setTimeout(function(){window.location.reload();},1000);</script>';
		}
		if($reload) {
			echo '<script>setTimeout(function(){window.location.href="'.$reload.'";},100);</script>';
		}
		if(!$head) {
			echo '</body></html>';
		}
	}
	
	private function prepareExistsInLog($filename=false)
	{
		if($filename===false) $filename=$this->filelist;
		if(is_file($filename)) {
			$lines=file($filename, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
			if($filename===$this->filelist) {
				static::$cache['filelist_count']=count($lines);
			}
			if(!empty($lines)) {
				static::$ecache[md5($filename)]=array_fill_keys($lines, 1);
			}
		}
	}
	
	private function existInLog($path, $filename=false)
	{
		if($filename===false) $filename=$this->filelist;
		if(!isset(static::$ecache[md5($filename)][$path])) {
			$output=[];
			$path=$this->npath($path, false);
			exec("grep -cx '{$path}' {$filename}", $output);
			$exists=((int)$output[0] > 0);
			if($exists) {
				static::$ecache[md5($filename)][$path]=1;
			}
			return $exists;
		}
		return true;
	}
	
	private function prepareExistsDirInLog()
	{
		if(is_file($this->dirlist)) {
			$lines=file($this->dirlist, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
			if(!empty($lines)) {
				static::$edcache=array_fill_keys($lines, 1);
			}
		}
	}
	
	private function existDirInLog($dirname)
	{
		if(!isset(static::$edcache[$dirname])) {
			$output=[];
			$dirname=$this->npath($dirname, false);
			exec("grep -cx '{$dirname}' {$this->dirlist}", $output);
			$exists=((int)$output[0] === 1);
			if($exists) {
				static::$edcache[$dirname]=1;
			}
			return $exists;
		}
		return true;
	}
	
	/**
	 * получить значение конфигурации
	 */
	private function cget($name, $default=0)
	{
		if(array_key_exists($name, $this->config)) {
			return $this->config[$name];
		}
		return $default;
	}
	
	private function cset($name, $value)
	{
		$this->config[$name]=$value;
		$this->write_ini_file($this->configfile, $this->config);
	}
	
	private function loadconfig()
	{
		if(!is_file($this->configfile)) {
			file_put_contents($this->configfile, '');
		}
		$this->config=parse_ini_file($this->configfile);
	}
	
	private function getlast($default=false, $returnAll=false)
	{
		if(is_file($this->filelist)) {
			$output=[];
			exec('tail ' . $this->filelist, $output);
			if($returnAll) return $output;
			return $this->npath(end($output), false);
		}
		return $default;
	}
	
	private function getTotalFiles()
	{
		$total=$this->cget('total_files', -1);
		if($total < 0) {
			$output=[];
			exec("find {$this->root} -type f -size -".self::READ_MAXSIZE."c | wc -l", $output);
			$total=(int)$output[0];
			$this->cset('total_files', $total);
		}
		return $total;
	}
	
	private function append($entry)
	{
		$entry=$this->npath($entry, false);
		if(($entry !== __FILE__) && ($entry !== $this->filelist)) {
			static::$ecache[md5($this->filelist)][$entry]=1;
			file_put_contents($this->filelist, $entry."\n", FILE_APPEND);
		}
	}
	
	private function appendDir($entry)
	{
		$entry=$this->npath($entry, false);
		static::$edcache[$entry]=1;
		file_put_contents($this->dirlist, $entry."\n", FILE_APPEND);
	}
	
	private function getVirusFileListFilename()
	{
		return dirname($this->virusfile) . '/' . pathinfo($this->virusfile, PATHINFO_FILENAME) . '_filelist.log';
	}
	
	private function appendVirus($entry, $prefix='', $postfix='')
	{
		$entry=$this->npath($entry);
		file_put_contents($this->virusfile, date('[Y.m.d H:i:s]').$prefix.' '.$entry."\n".$postfix, FILE_APPEND);
		file_put_contents($this->getVirusFileListFilename(), $entry."\n", FILE_APPEND);
	}
	
	/**
	 * Прочитать директорию.
	 */
	private function read($dirname, $return=true, $recursive=false, $isroot=true)
	{
		
		if(is_file($dirname)) {
			$dirname=rtrim(dirname($dirname), '/');
		}
		
		// unix
		exec("find {$dirname} -type f -size -".self::READ_MAXSIZE."c > {$this->filelist}");
		return $isroot;
		
		if(is_dir($dirname) && !$this->existDirInLog($dirname)) {
			$d = dir($dirname);
			while (false !== ($entry = $d->read())) {
				if(($entry != '.') && ($entry != '..')) {
					$entry=$this->npath($dirname. DIRECTORY_SEPARATOR . $entry, false);
					if(is_dir($entry) && $this->existDirInLog($entry)) {
						continue;
					}
					elseif(is_file($entry) && ((filesize($entry)>self::READ_MAXSIZE) || $this->existInLog($entry))) {
						continue;
					}
					
					$this->cnt++;
					if(($return===true) 
						|| (($return===static::RETURN_FILES) && is_file($entry))
						|| (($return===static::RETURN_DIRS) && is_dir($entry)))
					{
						$this->append($entry);
					}
					
					if($this->cnt > $this->limit) {
						$d->close();
						return false;
					}
					
					if($recursive && is_dir($entry)) {
						$this->read($entry, $return, $recursive, false);
					}
				}
			}
			$this->appendDir($dirname);
			$d->close();
		}
		
		return $isroot;
	}
	
	private function scan()
	{
		if(is_file($this->filelist)) {
			$files=file($this->filelist, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
			static::$cache['filelist_count']=count($files);
			if(!empty($files)) {
				$scanpos=(int)$this->cget('scan_pos', 0);
				
				if($scanpos || ($scanpos===0)) {
					$files=array_slice($files, $scanpos, $this->scanlimit);
				}
				
				foreach($files as $filename) {
					$filename=$this->npath($filename, false);
					if(is_file($filename) && !$this->existInLog($filename, $this->getVirusFileListFilename())) {
						$founds=$this->search($filename);
						if(is_array($founds)) {
							$info=$founds;
							$info['filename']=$filename;
							static::$cache['virus_founds'][]=$info;
							
							$this->cset('virus_count', (int)$this->cget('virus_count', 0) + 1);
							
							$prefix='';
							$postfix='';
							if(count($founds['rules'])>0) {
								$prefix.='s';
								foreach($founds['rules'] as $data) {
									$postfix.="Правило: ".$data['rule']."\n";
									$postfix.="Найдено: ".$data['count']."\n";
									foreach($data['parts'] as $part) {
										$postfix.='Позиция: ' . $part['pos'] . "\n\n";
										$postfix.=$part['code'] . "\n\n";
									}
								}
							}
							if(count($founds['irules'])>0) $prefix.='i';
							if(count($founds['rrules'])>0) $prefix.='r';
							$this->appendVirus($filename, $prefix?"[{$prefix}]":'', $postfix);
						}
					}
				}
						
				$this->cset('scan_pos', (int)$this->cget('scan_pos', 0) + count($files));
				if(count($files) >= $this->scanlimit) {
					return false;
				}
			}
			return true;
		}
		return null;
	}
	
	private function search($filename)
	{
		if(is_file($filename)) {
			$founds=[];
			$lastcontent='';
			$h=fopen($filename, "r");
			while(!feof($h)) {
				$content=fread($h, self::READ_FILESIZE);
				$result=$this->scanContent($lastcontent.$content);
				if(is_array($result)) {
					$founds=array_merge($founds, $result);
				}
			}
			fclose($h);
			
			if(empty($founds)) return false;
			return $founds;
		}
		return null;
	}
	
	private function scanContent($content)
	{
		$found=false;
		$founds=['rules'=>[], 'irules'=>[], 'rrules'=>[]];
		if(count($this->rules()) > 0) {
			foreach($this->rules() as $rule) {
				if(($pos=mb_strpos($content, $rule)) !== false) {
					$count=mb_substr_count($content, $rule);
					$parts=[];
					for($i=0; $i<$count; $i++) {
						$start=$pos-$this->coderange;
						if($start<0) $start=0;
						$code=mb_substr($content, $start, 2*$this->coderange);
						$parts[]=['pos'=>$pos, 'code'=>$code];
						$pos=mb_strpos($content, $rule, $pos+1);
						if($pos===false) break;
					}
					$founds['rules'][]=['pos'=>$pos, 'rule'=>$rule, 'count'=>$count, 'parts'=>$parts];
					$found=true;
				}
			}
		}
		if(count($this->irules()) > 0) {
			
		}
		if(count($this->rrules()) > 0) {
            if(count($this->rrules()) > 0) {
                foreach($this->rrules() as $rule) {
                    if(preg_match_all($rule, $content, $mparts, PREG_SET_ORDER)) {
                        $count=count($mparts);
                        $parts=[];
                        for($i=0; $i<$count; $i++) {
                            $parts[]=['pos'=>-1, 'code'=>$mparts[$i][0]];
                        }
                        $founds['rules'][]=['pos'=>-1, 'rule'=>$rule, 'count'=>$count, 'parts'=>$parts];
                        $found=true;
                    }
                }
            }			
		}
		
		if($found) {
			return $founds;
		}
		return false;
	}
	
 	private function log($var)
 	{
 		file_put_contents($this->logfile, date('[Y.m.d H:i:s] ').var_export($var, true)."\n", FILE_APPEND);
 	}
 	
 	/**
 	 * @link https://stackoverflow.com/a/48433241
 	 * 
 	 * Write an ini configuration file
     * 
     * @param string $file
     * @param array  $array
     * @return bool
     */
    private function write_ini_file($file, $array = []) {
        // check first argument is string
        if (!is_string($file)) {
            throw new \InvalidArgumentException('Function argument 1 must be a string.');
        }

        // check second argument is array
        if (!is_array($array)) {
            throw new \InvalidArgumentException('Function argument 2 must be an array.');
        }

        // process array
        $data = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $data[] = "[$key]";
                foreach ($val as $skey => $sval) {
                    if (is_array($sval)) {
                        foreach ($sval as $_skey => $_sval) {
                            if (is_numeric($_skey)) {
                                $data[] = $skey.'[] = '.(is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"'.$_sval.'"'));
                            } else {
                                $data[] = $skey.'['.$_skey.'] = '.(is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"'.$_sval.'"'));
                            }
                        }
                    } else {
                        $data[] = $skey.' = '.(is_numeric($sval) ? $sval : (ctype_upper($sval) ? $sval : '"'.$sval.'"'));
                    }
                }
            } else {
                $data[] = $key.' = '.(is_numeric($val) ? $val : (ctype_upper($val) ? $val : '"'.$val.'"'));
            }
            // empty line
            $data[] = null;
        }

        // open file pointer, init flock options
        $fp = fopen($file, 'w');
        $retries = 0;
        $max_retries = 100;

        if (!$fp) {
            return false;
        }

        // loop until get lock, or reach max retries
        do {
            if ($retries > 0) {
                usleep(rand(1, 5000));
            }
            $retries += 1;
        } while (!flock($fp, LOCK_EX) && $retries <= $max_retries);

        // couldn't get the lock
        if ($retries == $max_retries) {
            return false;
        }

        // got lock, write data
        fwrite($fp, implode(PHP_EOL, $data).PHP_EOL);

        // release lock
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }
    
    private function controls()
    {
    	$verifeds=[];
    	if(is_file($this->verifedfile)) {
    		$verifeds=file($this->verifedfile, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    	}
    	if(is_file($this->getVirusFileListFilename())) {
    		$virusfiles=file($this->getVirusFileListFilename(), FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    	}
		if(!empty($virusfiles)) {
			?>
			<style>
			#kscan_controls { margin-top: 50px; margin-bottom:50px;}
			#kscan_controls table td:not(:last-child) {text-align:center;}
			#kscan_controls table tr:nth-child(odd){background-color:#eee;}
			#kscan_controls table tr:hover{background-color:#ccc;}
			#kscan_controls table tr.verifed{background-color:#8BC34A;}
			#kscan_controls table tr.verifed:hover{background-color:#CDDC39;}
			#kscan_controls table tr.checked{background-color:#FF9800 !important;}
			#kscan_controls table tr.checked:hover{background-color:#FFC107 !important;}
			#kscan_controls table tr.preverifed{background-color:#afd681;}
			#kscan_controls table tr.preverifed:hover{background-color:#CDDC39;}
			#kscan_controls table td.verifed{cursor:pointer;}
			#kscan_controls table td.checkbox{cursor:pointer;}
			#kscan_controls table td.checkbox.deleted {background: #FF9800;}
			#kscan_controls table tr.deleted {opacity: 0.5;}
			#kscan_controls .panel, #kscan_controls .panel-footer {
				position: fixed;
			    background-color: #2196F3;
			    border: 1px solid #000;
    			width: 400px;
				z-index: 999;
			}
			#kscan_controls .panel {
				top: 0;
    			border-top: 0;
    			width: 900px;
			}
			#kscan_controls .panel .control {
				width: 45%;
			    float: left;
			    padding: 10px;
			}
			#kscan_controls .panel-footer {
				bottom: 0;
    			border-bottom: 0;
    			padding: 10px;
			}
			.fancybox-slide > div {
			    position: fixed !important;
			    top: 50px !important;
			    width: 80% !important;
			    left: 10% !important;
			    height: 80% !important;
			}
			</style>
			<script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js"></script>
			<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css" />
			<script>
			$(document).ready(function(){
				function getCheckedValues($items){
					if($items.length > 0) {
						var values=[];
						$items.each(function(){if($(this).prop("checked")) values.push($(this).val());});
					}
					else {
						var values=1;
					}
					return values;
				}
				function getCheckedDataFileNames($items){
					var values=[];
					if($items.length > 0) {
						$items.each(function(){if($(this).prop("checked")) values.push($(this).data("filename"));});
					}
					return values;
				}
				function toogleclass($e,classname){$e.hasClass(classname)?$e.removeClass(classname):$e.addClass(classname);}
				function onVerifedClick(e){
					var $p=$(e.target).parents("tr:first");
					if($p.hasClass("preverifed")) {$p.removeClass("preverifed");$p.addClass("verifed");}
					toogleclass($(e.target).parents("tr:first"),"verifed");
				}
				function onCheckboxClick(e){toogleclass($(e.target).parents("tr:first"),"checked");}
				function onTdClick(e){$(e.target).find(":checkbox").click();}
				function onBtnSaveClick(e){
					if(confirm("Подтвердите сохранение состояния проверенных файлов")) {
						$.post("?save&<?=time()?>", {files: getCheckedValues($(".verifed [type='checkbox']:checked"))}, function(response){
							var $trs=$("#kscan_table tr.verifed");$trs.addClass("preverifed");$trs.removeClass("verifed");
							// alert("Успешно сохранено!");
						});
					}
				}
				function onBtnDeleteClick(e){
					var files=getCheckedDataFileNames($(".checkbox [type='checkbox']:checked"));
					if(confirm("Подтвердите удаление отмеченных файлов\n"+files.join("\n"))) {
						$.post("?delete&<?=time()?>", {files: getCheckedValues($(".checkbox [type='checkbox']:checked"))}, function(response){
							alert(response);
							window.location.reload();
						});
					}
				}
				function onBtnRescanClick(e){
					if(confirm("Подтвердите перезапуск сканирования")) {
						window.location.href="<?=preg_replace('#^([^?]+)\?(.*)$#', '\1', $_SERVER['REQUEST_URI']).'?rescan=1&'.time()?>";
					}
				}
				function getFilterText(){return $("#kscan_filter_text").val();}
				function checkIt(cssclass){
					var r=new RegExp("^.*"+getFilterText()+".*$");
					$("#kscan_table ."+cssclass+" :checkbox").each(function(){
						if($(this).val().match(r) && !$(this).prop("checked")) {
							$(this).trigger("click");
						}
					});
				}
				function onBtnCheckedResetClick(e){$("#kscan_table tr:not(.preverifed) :checked").prop("checked", false);$("#kscan_table tr").removeClass("verifed checked");}
				function onBtnCheckedDeleteClick(e){checkIt("checkbox");}
				function onBtnCheckedSaveClick(e){checkIt("verifed");}
				$(document).on("click", ".verifed [type='checkbox']", onVerifedClick);
				$(document).on("click", ".checkbox [type='checkbox']", onCheckboxClick);
				$(document).on("click", "td.verifed", onTdClick);
				$(document).on("click", "td.checkbox", onTdClick);
				$(document).on("click", ".btn-save", onBtnSaveClick);
				$(document).on("click", ".btn-delete", onBtnDeleteClick);
				$(document).on("click", ".btn-rescan", onBtnRescanClick);
				$(document).on("click", ".btn-checked-delete", onBtnCheckedDeleteClick);
				$(document).on("click", ".btn-checked-save", onBtnCheckedSaveClick);
				$(document).on("click", ".btn-checked-reset", onBtnCheckedResetClick);
				$("[data-fancybox]").fancybox({type:"ajax",ajax:{type:"post"},animationEffect:false,transitionEffect:false,spinnerTpl:"<div>Загрузка...</div>"});
			});
			</script>
			<div id="kscan_controls">
				<div class="panel">
					<div class="control">
						<button class="btn-save">Сохранить</button>
						<button class="btn-delete" style="float:right;color:#F44336;">Удалить</button>
						<button class="btn-rescan" style="margin-right:10px;float:right;">Перезапустить сканирование</button>
					</div>
					<div class="filter" style="float:right;margin-left:30px;background:#FFC107;padding: 10px;">
						<span style="font-size:14px;color:#000;font-family:Arial;">Маска <input id="kscan_filter_text" type="text" style="width:100px"></span>
						<button class="btn-checked-delete" style="">Удаление</button>
						<button class="btn-checked-save" style="">Сохранение</button>
						<button class="btn-checked-reset" style="">Сбросить</button>
					</div>
				</div>
				<div class="panel-footer">
					<button class="btn-save">Сохранить</button>
					<button class="btn-delete" style="float:right;color:#F44336;">Удалить</button>
					<button class="btn-rescan" style="margin-right:10px;float:right;">Перезапустить сканирование</button>
				</div>
				<table border="1" id="kscan_table">
				<thead>
					<tr><th colspan="5" style="background-color:#ccc;text-align:left">Список подозрительных файлов</th></tr>
					<tr><td colspan="5" style="background-color:#eee;text-align:left;padding:5px 0"><b>Коневая директория:</b> <?=dirname(__FILE__)?></td></tr>
					<tr style="text-align:left">
						<th style="width:40px">Удалить</th>
						<th style="width:40px">&nbsp;</th>
						<th style="width:40px">Проверено</th>
						<th style="width:40px">&nbsp;</th>
						<th>Имя файла</th>
					</tr>
				</thead>
				<tbody>
				<?
				foreach($virusfiles as $filename) {
					if(($filename===__FILE__) || ($filename===$this->virusfile)) continue;
					$verifed=in_array($filename, $verifeds);
					$isfile=is_file($filename);
					?><tr<?=($isfile&&$verifed)?' class="preverifed"':''?><?=$isfile?'':' class="deleted"'?>>
						<td class="checkbox<?=$isfile?'':' deleted'?>">
							<? if($isfile): ?><input type="checkbox" data-filename="<?=preg_replace('#^'.dirname(__FILE__).'#', '', $filename)?>" value="<?=$filename?>"/><? else: ?>удален<? endif; ?>
						</td>
						<td><? if($isfile): ?><a data-fancybox href="?view=<?=$filename?>&<?=time()?>">просмотр</a><? else: ?>&nbsp;<? endif; ?></td>
						<td class="verifed"><? if($isfile): ?><input type="checkbox"<?=$verifed?' checked="checked"':''?> value="<?=$filename?>"/><? else: ?>&nbsp;<? endif; ?></td>
						<td><? if($isfile): ?><a target="_blank" href="?edit=<?=$filename?>&<?=time()?>">править</a><? else: ?>&nbsp;<? endif; ?></td>
						<td><?=preg_replace('#^'.dirname(__FILE__).'#', '', $filename)?></td>
					</tr><?
				}
			?></tbody></table><?
		}
    }
    
    private function hsc($text)
    {
    	return preg_replace(['/</','/>/'], ['&lt;', '&gt;'], $text);
    }
    
    private function view($filename)
    {
    	if(is_file($filename) && $this->existInLog($filename, $this->getVirusFileListFilename())) {
    		echo '<div>';
    		$this->printVirusInfo($filename);
			echo '<pre style="white-space:pre-wrap !important;">';
    		echo $this->hsc(file_get_contents($filename));
    		echo '</pre></div>';
    	}
    	else {
    		header('HTTP/1.0 403 Forbidden');
    		exit;
    	}
    }
    
    private function printVirusInfo($filename)
    {
    	$founds=$this->search($filename);
		if(is_array($founds)) {
			?>
			<style>.view-info {
			    background: #b8e9ff;
			    color: #333;
			    padding: 10px;
			    width: 80%;
			    margin: 0 auto 20px;
			    border-radius: 5px;
			    font-size: 14px;
			}
			.view-info-header {
			    font-weight: bold;
			}
			.view-info ul {
				margin: 0;
			}
			</style>
			<div class="view-info"><div class="view-info-header">Информация: <?=preg_replace('#^'.dirname(__FILE__).'#', '', $filename)?></div><ul><?
			if(count($founds['rules'])>0) {
				foreach($founds['rules'] as $data) {
					echo '<li>',
					'найдено: ',(int)$data['count'],', ',
					'правило: ',$this->hsc($data['rule']),', ',
					'позиция: ';
					foreach($data['parts'] as $i=>$part) echo ($i?',':''),$part['pos'];
					'</li>';
				}
			}
//				if(count($founds['irules'])>0);
//				if(count($founds['rrules'])>0);
			?></ul></div><?
		}
		else {
			?>
			<style>.view-info {
			    background: #8BC34A;
			    color: #333;
			    padding: 10px;
			    width: 80%;
			    margin: 0 auto 20px;
			    border-radius: 5px;
			    font-size: 14px;
			}
			.view-info-header {
			    font-weight: bold;
			}
			</style>
			<div class="view-info">
				<div class="view-info-header">Информация: <?=preg_replace('#^'.dirname(__FILE__).'#', '', $filename)?></div>
				Подозрительного кода не найдено!
			</div>
			<?
		}
    }
    
    private function save()
    {
    	if(array_key_exists('files', $_POST)) {
    		file_put_contents($this->verifedfile, '');
    		if(is_array($_POST['files']) && (count($_POST['files'])>0)) {
	    		foreach($_POST['files'] as $filename) {
	    			if(is_file($filename) 
	    				&& $this->existInLog($filename, $this->getVirusFileListFilename()) 
	    				&& !$this->existInLog($filename, $this->verifedfile)) 
	    			{
	    				file_put_contents($this->verifedfile, $filename."\n", FILE_APPEND);
	    			}
	    		}
    		}
    	}
    }
    
    private function remove()
    {
    	$errors=[];
    	$deleted=[];
    	if(array_key_exists('files', $_POST)) {
    		if(is_array($_POST['files'])) {
    			foreach($_POST['files'] as $filename) {
    				if(is_file($filename) && $this->existInLog($filename, $this->getVirusFileListFilename())) {
    					@unlink($filename);
    					if(is_file($filename)) {
    						$errors[]='ОШИБКА! ФАЙЛ НЕ УДАЛЕН! ' . $filename;
    					}
    					else {
    						$deleted[]=$filename;
    					}
    				}
    				else {
    					$errors[]='Файл в списке не найден. ' . $filename;
    				}
    			}
    		}
    		else {
    			$errors[]='Не найдено файлов для удаления.';
    		}
    	}
    	else {
    		$errors[]='Не найдено файлов для удаления.';
    	}
    	
    	if($errors) {
    		echo 'При удалении возникли ошибки!';
    		echo "\n" . implode("\n", $errors) . "\n";
    	}
    	if($deleted) {
    		echo 'Файлы успешно удалены!';
    		echo "\n" . implode("\n", $deleted);
    	}
    }
    
    private function edit($filename)
    {
    	if(is_file($filename) && $this->existInLog($filename, $this->getVirusFileListFilename())) {
    		$notify='';
    		if(isset($_POST['content'])) {
    			file_put_contents($filename, $_POST['content']);
    			?>
    			<style>
    			.notify{
    				background: #8BC34A;
				    color: #333;
				    padding: 10px;
				    text-align: center;
				    width: 80%;
				    margin: 10px auto;
				    border-radius: 5px;
    			}	
    			a {
    				display: block;
				    margin: 10px auto;
				    text-align: center;
				    margin-top: 40px;
    			}
    			</style>
    			<div class="notify">Файл "<?=$filename?>" успешно сохранен!</div>
    			<a href="?edit=<?=$filename?>&<?=time()?>">Продолжить редактирование</a>
    			<a href="javascript:;" onclick="window.close()">Закрыть окно</a>
    			<?
    		}
    		else {
    		?>
    		<h1>Редактирование файла "<?=$filename?>"</h1>
    		<form method="post">
    			<div class="warning">ВНИМАНИЕ! ВЫ РЕДАКТИРУЕТЕ ФАЙЛ В КОДИРОВКЕ UTF-8</div>
    			<?$this->printVirusInfo($filename);?>
    			<textarea name="content" style="width:100%;height:70%;border:2px solid #000;"><?=file_get_contents($filename)?></textarea>
    			<center>
	    			<input type="submit" value="Сохранить" onclick="return confirm('Подтвердите сохранение файла\n<?=$filename?>')"/>
	    			<button onclick="window.close()">Закрыть окно</button>
    			</center>
    		</form>
    		<style>
    			[type=submit]{
    				margin: 10px auto;
    				display: inline-block;
				    font-size: 24px;
				    background-color: #4CAF50;
				    border: 1px solid #000;
				    padding: 5px 20px;
				    color: #fff;
				    cursor: pointer;
    			}
    			[type=submit]:hover{opacity:0.7;}
    			.warning {
    				background: #FFC107;
				    color: #333;
				    padding: 10px;
				    text-align: center;
				    width: 50%;
				    margin: 10px auto;
				    border-radius: 5px;
    			}
    			button{
    				margin: 10px auto;
				    display: inline-block;
				    font-size: 24px;
				    background-color: #9E9E9E;
				    border: 1px solid #000;
				    padding: 5px 20px;
				    color: #fff;
				    cursor: pointer;
				    margin-left: 20px;
    			}
    		</style>
    		<?
    		}
    	}
    	else {
    		header('HTTP/1.0 403 Forbidden');
    		exit;
    	}
    }
}
