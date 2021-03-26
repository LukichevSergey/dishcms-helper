<?php

class modJshoppingNormalizeSefHelper
{
	const SECRET_NAME='VdnVbZHLdv7bGnBGansR3ZU4QCVpRPVFQW8yh6fscbUFLRMYhbR34SaXC2d6GQJr';
	const SECRET_VALUE='EtKC7dAufAzzU8FhNhwP8SKDspRqxu9YP6dHRmZ9n6qpDVKUTF7DxTm6kpqyw43z';
	const SECRET_COOKIE_VALUE='VPD8V624MxyYE88zUfZzJsQESvhnnMKJmPsvcdHLGGP6Bvz7BLwW2menBzdjzsjb';
	const SECRET_COOKIE_NAME='jshopnsef';

	public static function normalize()
	{
	    if(($_POST[self::SECRET_NAME] === self::SECRET_VALUE) && ($_COOKIE[self::SECRET_COOKIE_NAME]===self::SECRET_COOKIE_VALUE)) {
	        setcookie(self::SECRET_COOKIE_NAME);
    	    
    	    // нормализация
    	    $db=\JFactory::getDBO();
    	    $query='UPDATE `d4lwz_jshopping_products` SET `alias_ru-RU`=_fs_normalize_alias_ru(`product_id`, `alias_ru-RU`, `name_ru-RU`, `product_ean`);';
    	    $db->setQuery($query);
    	    $db->query();
    	    
    	    // генерация кэша фильтра товаров
    	    $ch=curl_init('https://'.$_SERVER['SERVER_NAME'].'/shini?h=9UQvm9BjdmKKTsrX7EqpuRDr');
    	    curl_exec($ch);
    	    curl_close($ch);
    	    
    	    // карта сайта
    	    $ch=curl_init('https://'.$_SERVER['SERVER_NAME'].'/shini?h=BfTEnAqw9gUXLJDAKneJyYw');
    	    curl_exec($ch);
    	    curl_close($ch);
    	    
    	    echo '{{{modJshoppingNormalizeSef_OK}}}';
    	    
    	    exit;
		}
	}
}
