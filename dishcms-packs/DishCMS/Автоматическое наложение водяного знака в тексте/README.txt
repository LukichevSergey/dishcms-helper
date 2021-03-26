Заменить в index.php строку

ob_end_flush();

на PHP код:

$content=ob_get_clean();

echo preg_replace_callback('#"/images/product/(?!water_)([^>"\s]+)\.(jpg|png|jpeg)"#msxU', function($m) {
	return '"' . ResizeHelper::watermark(trim($m[0], '"')) . '"';
}, $content);
