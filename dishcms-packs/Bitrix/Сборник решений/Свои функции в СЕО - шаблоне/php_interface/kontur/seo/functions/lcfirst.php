<?php
/**
 * Первый символ в нижний регистр 
 *
 * пример {=lcfirst this.Name}
 */
\kontur\seo\SeoFunction::register('lcfirst', function($parameters, $result) {
	$text = (string)array_shift($result);
	// multi-byte ucfirst
	return mb_strtolower(mb_substr($text, 0, 1)) . mb_substr($text, 1);
});
