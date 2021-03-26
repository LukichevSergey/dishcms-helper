<?php
/**
 * Первый символ в верхний регистр 
 *
 * пример {=ucfirst this.Name}
 */
\kontur\seo\SeoFunction::register('ucfirst', function($parameters, $result) {
	$text = (string)array_shift($result);
	// multi-byte ucfirst
	return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
});
