<?php
/**
 * HTML helper
 */
class HtmlHelper extends CComponent
{
	/**
	 * Convert array of html tag attributes to string
	 * @param array $attributes html tag attributes.
	 * @param bool $forcibly include or not attributes with empty values into result string. 
	 * Default (false) not including. 
	 * @return string html tag attributes.
	 */
	public static function AttributesToString($attributes, $forcibly=false)
	{
		$_attributes = array();
		foreach($attributes as $attribute=>$value) {
			if($value || $forcibly) 
				$_attributes[] = $attribute . '="' . preg_replace('/\\\\*?"/', '\"', $value) . '"';
		}
		
		return empty($_attributes) ? '' : implode(' ', $_attributes);
	}
}