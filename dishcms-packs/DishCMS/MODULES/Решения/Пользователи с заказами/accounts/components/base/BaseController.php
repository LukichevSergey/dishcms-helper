<?php
namespace accounts\components\base;

use common\components\helpers\HYii as Y;
use common\components\helpers\HModel;

class BaseController extends \Controller
{
    /**
     * {@inheritDoc}
     * @see \CController::$layout
     */
    public $layout='//layouts/other';
    
    /**
     * Функция перевода
     * @var callable
     */
    protected static $t=null;
    
    /**
     * Получить функцию перевода
     * @return callable
     */
    public function t($message, $params=[])
    {
        if(static::$t === null) {
            $category=strtolower(preg_replace('/^.*?\\\\([^\\\\]+)Controller$/', '$1', get_called_class()));            
            static::$t=Y::ct('\AccountsModule.controllers/' . $category);
        }
        
        return call_user_func_array(static::$t, [$message, $params]); 
    }
    
    /**
     * Добавить "хлебную крошку" на главную страницу личного кабинета
     * @param boolean $disableUrl сделать "хлебную крошку" неактивной.
     * По умолчанию (FALSE) "хлебная крошка" активна.
     * @param string $title заголовок "хлебной крошки", будет получен 
     * с применением BaseController::t(). По умолчанию "account.home".
     */
    public function addHomeBreadcrumb($disableUrl=false, $title='account.home')
    {
        $this->breadcrumbs->add($this->t($title), $disableUrl ? null : '/accounts/account/index');
    }
    
    /**
     * Добавить "хлебную крошку" текущей страницы
     * @param string|null $title переопределить заголовок "хлебной крошки".
     * По умолчанию (NULL) не переопределять.
     */
    public function addPageBreadcrumb($title=null)
    {
        $this->breadcrumbs->add($this->pageTitle);
    }
}