<?php
/**
 * Нормализация ЧПУ товаров
 * 
 * @author     Kontur
 * @package    Jshopping
 * @license    GNU/GPL, see LICENSE.php
 * @link       http://docs.joomla.org/J3.x:Creating_a_simple_module/Developing_a_Basic_Module
 * mod_helloworld is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

require dirname(__FILE__).'/helper.php';

modJshoppingNormalizeSefHelper::normalize();