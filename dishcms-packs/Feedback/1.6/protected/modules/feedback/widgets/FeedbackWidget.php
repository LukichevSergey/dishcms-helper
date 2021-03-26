<?php
/**
 * Feedback module.
 * 
 * Feedback widget.
 * 
 */
namespace feedback\widgets;

use \AttributeHelper as A;
use \feedback\components\FeedbackFactory;

class FeedbackWidget extends \CWidget
{
	/**
	 * Feedback ID.
	 * 
	 * Должен существовать конфигурационный файл по данному ID
	 * в \feedback\configs\forms\{ID}.php
	 * 
	 * @var string
	 */
	public $id;
	
	/**
	 * Secondary Id для форм повторяющихся на странице, т.к. валидация и отправка 
	 * в Yii 1 \CActiveForm не выполняется корректно. 
 	 * @var string
	 */
	public $secondaryId = '';
	
	/**
	 * Title
	 * @var string
	 */
	public $title;
	
	/**
	 * User widget options 
	 * @var array
	 */
	public $options = array();

	public $params = array();
			
	/**
	 * Hash string
	 * @var string
	 */
	protected $_hash = '';
	
	/**
	 * Default widget options
	 * @var array
	 */
	protected $_options = array(
		'htmlOptions' => array(
			'class' => ''
		),
	);	
	
	/**
	 * Get hash
	 * @return string
	 */
	public function getHash()
	{
		return $this->_hash;
	}
	
	/**
	 * Get form id
	 * @return string
	 */
	public function getFormId()
	{
		return 'feedback-' . $this->id . '-form-' . $this->secondaryId . $this->getHash();
	}
	
	/**
	 * Get form action
	 * @return string form action
	 */
	public function getFormAction()
	{
		return '/feedback/' . $this->id . '/ajax/send/';
	}
	
	/**
	 * Get option value
	 * @param string $section section name.
	 * @param string $option option name.
	 * @return mixed option value
	 */
	public function getOption($section, $option) 
	{
		$options = A::get($this->_options, "{$section}Options");
		
		return $options ? A::get($options, $option) : null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::init()
	 */
	public function init()
	{
		// generate hash
		$this->_hash = $this->generateHash();
		
		// merge user options with default options
		$this->_options = \CMap::mergeArray($this->_options, $this->options);
		
		// publish assets
		\AssetHelper::publish(array(
			'path' 	=> \Yii::getPathOfAlias('feedback.widgets.assets'),
			'js' 	=> array('js/FeedbackWidget.js')
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 * 
	 */
	public function run()
	{	
		// @var \feedback\components\FeedbackFactory $factory
		$factory = FeedbackFactory::factory($this->id);
		
		$this->render('default', compact('factory'));
	}
	
	/**
	 * Returns the fully qualified name of this class.
	 * @link http://yiiframework.ru/forum/viewtopic.php?f=3&t=4976
	 * @author karminski. Author of post with this code.  
	 * @return string the fully qualified name of this class.
	 */
	public static function className()
	{
		return get_called_class();
	}
	
	/**
	 * Generate hash
	 * @return string hash string
	 */
	protected function generateHash()
	{
		return substr(md5($this->id), 0, 8);
	}
}