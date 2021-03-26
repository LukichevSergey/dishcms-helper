<?php
/**
 * Feedback module.
 * 
 * InputField widget.
 * 
 * Type: Captcha
 * 
 * 
 */
namespace feedback\widgets\inputField;

class Captcha extends \CCaptcha
{
	public function run()
	{
		$this->render('captcha');
	}
} 