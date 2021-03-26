<?php
/**
 * Файл имитации сервера 1с для обмена с сайтом.
 *
 * @link https://v8.1c.ru/tekhnologii/obmen-dannymi-i-integratsiya/standarty-i-formaty/protokol-obmena-s-saytom/
 */
set_time_limit(120);

define('EXCHANGE_CONVERT_UTF8', true);

if(isset($_POST['RUN'])) {
    class Exchange {
        const STATUS_SUCCESS=1;
        const STATUS_PROGRESS=2;
        const STATUS_FAILURE=3;
        const STATUS_UNKNOW=4;
        private static $url;
        private static $login;
        private static $password;
        private static $zip=false;
        private static $fileLimit=1048576;
        private static $filesDirectory='files';
        private static $response;
        private static $cookieName;
        private static $cookieValue;
        private static $logMessages=[];
        
        public static function convert($str)
        {
            return EXCHANGE_CONVERT_RESULT_UTF8 ? iconv('CP1251', 'UTF-8//IGNORE', $str) : $str;
        }

        public static function configure($url, $login=null, $password=null, $filesDirectory=null) 
        {
            static::$url=$url;
            static::$login=$login;
            static::$password=$password;
            static::$filesDirectory=$filesDirectory;

            return !empty(static::$filesDirectory);
        }

        public static function modeCheckAuth()
        {
            static::send([
                'type'=>'catalog',
                'mode'=>'checkauth'
            ]);            
            
            static::setCookies();
            
            return (static::getStatus() == self::STATUS_SUCCESS);
        }
        
        public static function modeInit()
        {
            static::send([
                'type'=>'catalog',
                'mode'=>'init'
            ]);
            
            $params=static::getResponseParams();
            
            static::$zip=!empty($params['zip']) && ($params['zip'] == 'yes');
            static::$fileLimit=!empty($params['file_limit']) ? (int)$params['file_limit'] : 0;
            
            static::log([static::$zip, static::$fileLimit], 'mode_init');
            
            return !!static::$fileLimit;
        }
        
        public static function modeFile($parent=null)
        {
            $success=true; 
            
            $path=dirname(__FILE__) . '/' . static::$filesDirectory . ($parent ? "/{$parent}" : '');            
            $iterator = new DirectoryIterator($path);
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile()) {
                    $filename=realpath($fileinfo->getPathname());
                    
                    if(static::$zip) {
                        $zip = new ZipArchive();
                        $filename=$fileinfo->getPath() . '/' . basename($fileinfo->getFilename()) . '.zip';
                        $rzip=$zip->open($filename, ZipArchive::CREATE);
                        if($rzip !== true) {
                            throw new \Exception('Не удалось создать временный zip файл');
                        }
                        $zip->addFile($fileinfo->getPathname(), $fileinfo->getFilename());
                        $zip->close();
                    }
                    
                    $fp=fopen($filename, 'r+');
                    while(!feof($fp)) {
                        $content=fread($fp, static::$fileLimit);
                        do {
                            static::send([
                                'type'=>'catalog',
                                'mode'=>'file',
                                'filename'=>($parent ? "{$parent}/" : '') . pathinfo($filename, PATHINFO_FILENAME)
                            ], $content);
                        }
                        while(static::getStatus() == self::STATUS_PROGRESS);
                    }
                    
                    fclose($fp);
                    
                    if(static::$zip) {
                        unlink($filename);
                    }
                    
                    if(static::getStatus() != self::STATUS_SUCCESS) {
                        $success=false;
                        break;
                    }
                }
                elseif($fileinfo->isDir() && ($fileinfo->getFilename() != '.') && ($fileinfo->getFilename() != '..')) {
                    static::modeFile(($parent ? "{$parent}/" : '') . $fileinfo->getFilename());
                }
            }

            return $success;
        }
        
        public static function modeImport()
        {
            $success=true; 
            
            $iterator = new DirectoryIterator(dirname(__FILE__) . '/' . static::$filesDirectory);
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile()) {
                    do {
                        static::send([
                            'type'=>'catalog',
                            'mode'=>'import',
                            'filename'=>$fileinfo->getFilename()
                        ]);
                    }
                    while(static::getStatus() == self::STATUS_PROGRESS);
                    
                    if(static::getStatus() != self::STATUS_SUCCESS) {
                        $success=false;
                        break;
                    }
                }
            }

            return $success;
        }
        
        private static function setCookies()
        {
            $result=explode("\n", static::$response);
            
            static::$cookieName=!empty($result[1]) ? $result[1] : null;
            static::$cookieValue=!empty($result[2]) ? $result[2] : null;
            
            static::log([static::$cookieName, static::$cookieValue], 'setCookie:name, value');
        }
        
        private static function getResponseParams()
        {
            $params=[];
            $result=explode("\n", static::$response);
            foreach($result as $data) {
                if(strpos($data, '=')) {
                   list($k, $v)=explode('=', $data); 
                   $params[$k]=$v;
                }
            }
            
            static::log($params, 'getResponseParams:params');

            return $params;
        }
        
        private static function getStatus() {
            $result=explode("\n", static::$response);
            if(!empty($result[0])) {
                switch($result[0]) {
                    case 'success': return self::STATUS_SUCCESS; break;
                    case 'progress': return self::STATUS_PROGRESS; break;
                    case 'failure': return self::STATUS_FAILURE; break;
                }
            }
            return self::STATUS_UNKNOW;
        }
        
        public static function send($dataGet=null, $dataPost=null) {
            $url=static::$url;
            if($dataGet) {
                $url.='?'.http_build_query($dataGet);
            }
            $ch=curl_init($url);
            
            static::log($url, 'send:url');

            if(static::$login && static::$password) {
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD,  static::$login . ':' . static::$password);

                static::log(static::$login . ':' . static::$password, 'send:CURLOPT_USERPWD');
            }
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if($dataPost) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);

                static::log($dataPost, 'send:dataPost');
            }
            
            if(static::$cookieName && static::$cookieValue) {
                curl_setopt($ch, CURLOPT_COOKIE, static::$cookieName . '=' . static::$cookieValue);

                static::log(static::$cookieName . '=' . static::$cookieValue, 'send:cookie[name=value]');
            }
            
            static::$response=curl_exec($ch);
            
            static::log(static::convert(static::$response), 'send:response');
            static::log(curl_errno($ch) . ':' . curl_error($ch), 'send:curl_errno():curl_error()');
            static::log(curl_getinfo($ch), 'send:curl_getinfo()');
            
            curl_close($ch);

            return static::$response;
        }

        public static function log($data, $label=null)
        {
            $message='[' . date('d.m.Y H:i:s') . '] ';
            $message.="--------------------------------------------------------------------------\n";
            if($label) {
                $message.=$label;
                $message.="\n------------------------------------------------------------------------------------------------\n";
            }
            ob_start();var_dump($data);$message.=ob_get_clean();
            //$message.=var_export($data, true);
            $message.="\n------------------------------------------------------------------------------------------------\n\n";
            
            // file_put_contents(dirname(__FILE__) . '/1c_exchange.log', $message, FILE_APPEND);
            
            static::$logMessages[]=$message;
        }

        public static function showLog()
        {
            if(!empty(static::$logMessages)) {
                echo '<pre>',
                    "------------------------------------------------------------------------------------------------\n",
                    "СООБЩЕНИЯ ЛОГИРОВАНИЯ\n",
                    "------------------------------------------------------------------------------------------------\n",
                    implode("\n", static::$logMessages);
                    '</pre>';
            }
        }
    }
    
    if(Exchange::configure($_POST['URL'], $_POST['LOGIN'], $_POST['PASSWORD'], $_POST['FOLDER'])) {
        // A. Начало сеанса
        if(Exchange::modeCheckAuth()) {
            // B. Запрос параметров от сайта
            if(Exchange::modeInit()) {
                // C. Выгрузка на сайт файлов обмена
                if(Exchange::modeFile()) {
                    // D. Пошаговая загрузка данных
                    Exchange::modeImport();
                }
            }
        }
    }
    else {
        echo '<div class="error">Ошибка! Указаны не все параметры для обмена.</div>';
    }
}
?>
<style>input[type=text],select{width:75%;margin-bottom:10px;}.error{color:#f00;margin:10px 0;}</style>
<form method="post">
    <?  $folders = [];
        foreach ((new DirectoryIterator(dirname(__FILE__))) as $fileinfo) {
            if ($fileinfo->isDir() && !$fileinfo->isDot()) { 
                $folders[]=$fileinfo->getFilename();
            }
        }
    ?>
    <select name="FOLDER" >
        <option value="">-- выберите директорию с файлами выгрузки --</option>
        <? foreach($folders as $folder) { ?><option value="<?=$folder?>"<? if($folder==@$_POST['FOLDER']){ echo 'selected'; } ?>><?=$folder?></option> <? } ?>
    </select>
    <br/>
    <input type="text" name="URL" placeholder="Адрес скрипта обмена" value="<?=@$_POST['URL']?>">
    <br/>
    <input type="text" name="LOGIN" placeholder="Логин" value="<?=@$_POST['LOGIN']?>">
    <br/>
    <input type="text" name="PASSWORD" placeholder="Пароль" value="<?=@$_POST['PASSWORD']?>">
    <br/>
    <input type="submit" name="RUN" value="Запустить обмен" />
</form>
<? if(class_exists('Exchange')) { Exchange::showLog(); } ?>