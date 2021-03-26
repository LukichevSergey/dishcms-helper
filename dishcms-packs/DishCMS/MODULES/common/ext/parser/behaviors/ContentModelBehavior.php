<?php
namespace common\ext\parser\behaviors;

use common\components\helpers\HDb;
use common\ext\parser\components\helpers\HHttp;
use common\ext\parser\components\exceptions\ContentException;
use crud\models\ar\common\ext\parser\models\Page;

class ContentModelBehavior extends \CBehavior
{
    /**
     * Объект страницы
     * @var Page
     */
    private $page;
    
    /**
     * Scope: по идентификатору страницы
     * 
     * @param integer $id идентификатор страницы
     * 
     * @return Content
     */
    public function byPageId($id)
    {
        $criteria=new \CDbCriteria();
        
        $criteria->addColumnCondition(['page_id'=>$id]);
        
        $this->owner->getDbCriteria()->mergeWith($criteria);
        
        return $this->owner;
    }
    
    /**
     * Scope: по текущей странице
     *
     * @return Group
     */
    public function byPage()
    {
        return $this->byPageId($this->getPage()->getId());
    }
    
    /**
     * Получить конфигурацию парсера
     * 
     * @return \common\ext\parser\models\Config
     */
    public function getConfig()
    {
        return $this->getPage()->getConfig();
    }
    
    /**
     * Получить объект страницы
     * 
     * @return Page
     */
    public function getPage()
    {
        if(!($this->page instanceof Page)) {
            throw new ContentException('Страница не найдена.');
        }
        
        return $this->page;
    }
    
    /**
     * Установить объект страницы
     * 
     * @param Page $group объект страницы
     * 
     * @return Content
     */
    public function setPage($page)
    {
        $this->page=$page;
        
        if($this->page) {
            $this->owner->page_id=$this->getPage()->getId();
        }
        
        return $this->owner;
    }
    
    /**
     * Получить содержимое страницы
     * 
     * @return string
     */
    public function getText($reload=false)
    {
        if($reload) {
            $this->load();
        }
        
        return $this->owner->text;
    }
    
    /**
     * Установить текст страницы
     * 
     * @return Content 
     */
    public function setText($text)
    {
        $this->owner->text=$text;
        
        return $this->owner;
    }
    
    /**
     * Загрузка текста страницы
     * 
     * @return Content
     */
    public function load()
    {
        if($delay=$this->getConfig()->getDelay()) {
            sleep($delay);
        }
        
        $text=HHttp::getContent($this->getPage()->getUrl());
        
        if(HHttp::lastError()) {
            throw new ContentException('Не удалось загрузить страницу.');
        }
        
        $this->setText($text);
        
        return $this->owner;
    }
}