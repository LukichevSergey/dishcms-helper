<?php
/**
 * Условие
 *
 * пример {=iff условие "?" значение_для_true ":" значение_для_false }
 * пример {=iff условие "?" значение_для_true }  
 */
\kontur\seo\SeoFunction::register('iff', array(
	'calculate' => function($parameters, $result) {
		if ( empty($parameters) ) {
			return null;
		}		
		
		$condition = array_shift($parameters);
		$then = array_shift($parameters);
		$true = array_shift($parameters);
		$else = array_shift($parameters);
		$false = array_shift($parameters);
		
		return (bool)$condition ? $true : $false;
	}
));

