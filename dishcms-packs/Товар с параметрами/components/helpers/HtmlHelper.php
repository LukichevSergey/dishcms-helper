<?php
/**
 * HTML helper
 * 
 * @version 1.0
 * 
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
	
	/**
	 * Ссылка на предыдущую страницу
	 * @param string $text текст ссылки
	 * @return string
	 */
	public static function linkBack($text='Go to back') 
	{
		return CHtml::link($text, \Yii::app()->request->urlReferrer);
	}
	
	/**
	 * Print yii application user flash.
	 * @see \CWebUser::getFlash()
	 * @param string $key 
	 * @param string $defaultValue
	 * @param boolean $delete
	 * 
	 * @param string $return Возвращать или нет HTML-код сообщения. 
	 * @return string|void
	 */
	public static function flash($key, $defaultValue=NULL, $delete=true, $return=false)
	{
		$html = '';
		
		if(\Yii::app()->user->hasFlash($key)) {
			$html = "<div class=\"flashMessage {$key}\">";
			$html .= \Yii::app()->user->getFlash($key);
			$html .= '</div>';
		}
		
		if($return) return $html;
		
		echo $html;
	}
}