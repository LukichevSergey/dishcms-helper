<link rel="canonical" href="<?=$this->createAbsoluteUrl('/').preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI'])?>" />
<link rel="canonical" href="<?=$this->createAbsoluteUrl('/').rtrim(preg_replace('/(\?.*)?([?&]page=[^=]*)(.*)$/', '$1$3', $_SERVER['REQUEST_URI']), '?')?>" />
