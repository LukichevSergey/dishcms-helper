<?
namespace Kontur\Core\Main\Tools;

class Hash
{
	/**
     * Простое обратимое шифрование. Шифрование.
     * @see http://qaru.site/questions/728132/simple-encryption-in-php
     * @param mixed $data данные для шифрования
     * @param string $key ключ шифрования
     * @return string
     */
    public static function srEcrypt($data, $key=''){
        $str=json_encode($data, JSON_UNESCAPED_UNICODE);
        $result='';
        $keylen=strlen($key);
        $strlen=3*strlen($str);
        for($i=0; $i<$strlen; $i++) {
            if(!isset($str[$i])) break;
            $char=$str[$i];
            $keypos=$keylen ? (($i % $keylen)-1) : 0;
            $keychar=(($keypos > $keylen) || ($keypos < 0)) ? '@' : $key[$keypos];
            $char=chr(ord($char)+ord($keychar));
            $result.=$char;
        }
        return urlencode(base64_encode($result));
    }
    
    /**
     * Простое обратимое шифрование. Разшифрование.
     * @see http://qaru.site/questions/728132/simple-encryption-in-php
     * @param string $str зашифрованная строка методом HHash::simpleEcrypt
     * @param string $key ключ шифрования
     * @param boolean $assoc true - возвращать ассоциативный массив (по умолчанию), 
     * false - возвращать объект.
     * @return mixed
     */
    public static function srDecrypt($str, $key='', $assoc=true){
        $str=base64_decode(urldecode($str));
        $result='';
        $keylen=strlen($key);
        $strlen=3*strlen($str);
        for($i=0; $i<$strlen; $i++) {
            if(!isset($str[$i])) break;
            $char=$str[$i];
            $keypos=$keylen ? (($i % $keylen)-1) : 0;
            $keychar=(($keypos > $keylen) || ($keypos < 0)) ? '@' : $key[$keypos];
            $char=chr(ord($char)-ord($keychar));
            $result.=$char;
        }
        return @json_decode($result, $assoc);
    }
    
    /**
     * OpenSSL. Простое обратимое шифрование. Шифрование.
     * @param mixed $data данные для шифрования
     * @param string $key ключ шифрования
     * @param string $method метод шифрования. По умолчанию "AES-256-CFB".
     * @return string
     */
    public static function sslEcrypt($data, $key='', $method='AES-256-CFB')
    {
        $str=json_encode($data, JSON_UNESCAPED_UNICODE);
        
        $result=openssl_encrypt($str, $method, $key);
        
        return urlencode(base64_encode($result));
    }
    
    /**
     * OpenSSL. Простое обратимое шифрование. Разшифрование.
     * @param string $str зашифрованная строка методом HHash::simpleEcrypt
     * @param string $key ключ шифрования
     * @param boolean $assoc true - возвращать ассоциативный массив (по умолчанию), 
     * false - возвращать объект.
     * @param string $method метод шифрования. По умолчанию "AES-256-CFB".
     * @return string
     */
    public static function sslDecrypt($str, $key='', $assoc=true, $method='AES-256-CFB')
    {
        $str=base64_decode(urldecode($str));
        
        $result=openssl_decrypt($str, $method, $key);
        
        return @json_decode($result, $assoc);
    }
}