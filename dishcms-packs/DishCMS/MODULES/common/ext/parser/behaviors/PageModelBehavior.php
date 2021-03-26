<?php
namespace common\ext\parser\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HDom;
use common\components\helpers\HDb;
use common\ext\parser\components\helpers\HStatus;
use common\ext\parser\components\exceptions\PageException;
use crud\models\ar\common\ext\parser\models\Process;
use crud\models\ar\common\ext\parser\models\Group;
use crud\models\ar\common\ext\parser\models\Page;
use crud\models\ar\common\ext\parser\models\Content;

class PageModelBehavior extends \CBehavior
{
    /**
     * Объект контента страницы
     * @var Content
     */
    private $content;
    
    /**
     * Объект группы процесса парсинга
     * @var Group
     */
    private $group;
    
    
    /**
     * Родительская страница
     * @var Page
     */
    private $parent;
    
    /**
     * Кэш для создания полей хэша синхронизации
     * @var array массив вида array(syncAttribute=>bool)
     */
    private static $syncColumnExists=[];
    
    /**
     * Scope: по идентификатору группы процесса
     * 
     * @param int|[] $id идентификатор группы процесса.
     * Может быть передан массив идентификаторов групп процесса.
     * 
     * @return Page
     */
    public function byGroupId($id)
    {
        $criteria=new \CDbCriteria();
        
        if(is_array($id)) {
            $criteria->addInCondition('group_id', $id);
        }
        else {
            $criteria->addColumnCondition(['group_id'=>$id]);
        }
        
        $this->owner->getDbCriteria()->mergeWith($criteria);
        
        return $this->owner;
    }
    
    /**
     * Scope: по типу страницы
     * 
     * @param integer $type тип страницы
     * 
     * @return Page
     */
    public function byType($type)
    {
        $criteria=new \CDbCriteria();
        
        $criteria->addColumnCondition(['type'=>$type]);
        
        $this->owner->getDbCriteria()->mergeWith($criteria);
        
        return $this->owner;
    }
    
    /**
     * Получить идентификатор страницы
     * 
     * @return int
     */
    public function getId()
    {
        return (int)$this->owner->id;
    }
    
    /**
     * Получить идентификатор группы страницы
     * 
     * @return int
     */
    public function getGroupId()
    {
        return (int)$this->owner->group_id;
    }
    
    /**
     * Получить URL страницы
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->owner->url;
    }
    
    /**
     * Установить URL страницы
     * 
     * @param string $url URL страницы
     * 
     * @return Page
     */
    public function setUrl($url)
    {
        $this->owner->url=$this->normalizeUrl($url);
        $this->owner->hash=$this->generateHash();
        
        return $this->owner;
    }
    
    /**
     * Получить тип страницы
     * 
     * @return int
     */
    public function getType()
    {
        return (int)$this->owner->type;
    }
    
    /**
     * Установить тип страницы
     * 
     * @return Page
     */
    public function setType($type)
    {
        $this->owner->type=(int)$type;
        
        return $this->owner;
    }
    
    /**
     * Страница является входной страницей.
     * @return boolean
     */
    public function isEntry()
    {
        return ($this->getType() === Page::TYPE_ENTRY);
    }
    
    /**
     * Страница является внутренней страницей
     * @return boolean
     */
    public function isLink()
    {
        return $this->getType() == Page::TYPE_LINK;
    }
    
    /**
     * Страница является страницей пагинации
     * @return boolean
     */
    public function isPagination()
    {
        return $this->getType() == Page::TYPE_PAGINATION;
    }
    
    /**
     * Генерация значения хэша страницы
     * 
     * @param string|null $url URL страницы. 
     * По умолчанию (null) будет взят текущий.
     * 
     * @return string
     */
    public function generateHash($url=null)
    {
        if($url === null) {
            $url=$this->url;
        }
        
        return md5($url);
    }
    
    /**
     * Получить значение хэша для синхронизации данных
     * 
     * @param [] $data данные записи
     * 
     * @return string
     */
    public function getSyncHash($data)
    {
        $config=$this->getConfig();
        $groupConfig=$this->getGroup()->getGroupConfig();
        
        if($syncAttributes=$config->getGroupContentSyncAttributes($groupConfig)) {
            if(!is_string($syncAttributes) && !is_array($syncAttributes) && is_callable($syncAttributes)) {
                $data=call_user_func_array($syncAttributes, [$this->owner, $data]);
            }
            else {
                $hashData=[];
                $syncAttributes=A::toa($syncAttributes);
                foreach($syncAttributes as $attribute) {
                    if(A::existsKey($data, $attribute)) {
                        $hashData[$attribute]=$data[$attribute];
                    }
                }
                $data=$hashData;
            }
        }
        
        return md5(json_encode($data));
    }
    
    /**
     * Получить объект группы страницы
     * 
     * @return Group
     */
    public function getGroup()
    {
        if(!($this->group instanceof Group)) {
            throw new PageException('Группа не найдена.');
        }
        
        return $this->group;
    }
    
    /**
     * Установить объект группы страницы
     * 
     * @param Group $group объект группы страницы
     * 
     * @return Page
     */
    public function setGroup($group)
    {
        $this->group=$group;
        
        if($this->group) {
            $this->owner->group_id=$this->getGroup()->getId();
            $this->owner->process_id=$this->getGroup()->getProcess()->getId();
        }
        else {
            $this->owner->group_id=null;
        }
        
        return $this->owner;
    }
    
    /**
     * Получить иденификатор процесса
     * @return int
     */
    public function getProcessId()
    {
        return (int)$this->owner->process_id;
    }
    
    /**
     * Установить иденификатор процесса
     * 
     * @param int $processId иденификатор процесса
     * 
     * @return Page
     */
    public function setProcessId($processId)
    {
        $this->owner->process_id=(int)$processId;
        
        return $this->owner;
    }
    
    /**
     * Получить иденификатор родительской страницы
     * @return int
     */
    public function getParentPageId()
    {
        return (int)$this->owner->parent_page_id;
    }
    
    /**
     * Получить модель родительской страницы
     * 
     * @return Page|null
     */
    public function getParentPage()
    {
        if(!$this->parent && $this->getParentPageId()) {
            $this->parent=Page::model()->resetScope()->findByPk($this->getParentPageId());
        }
        
        return $this->parent;
    }
    
    /**
     * Получить конфигурацию парсера
     * 
     * @return \common\ext\parser\models\Config
     */
    public function getConfig()
    {
        return $this->getGroup()->getConfig();
    }
    
    /**
     * Получить объект контента страницы
     * 
     * @param bool $load загрузить контент страницы
     * 
     * @return Content
     */
    public function getContent($load=false)
    {
        if($load) {
            $this->loadContent();
        }
        
        if(!($this->content instanceof Content)) {
            throw new PageException('Контент страницы не найден.');
        }
        
        return $this->content;
    }
    
    /**
     * Установить объект контента страницы.
     * 
     * @param Content $content объект контента страницы.
     * 
     * @return Page
     */
    public function setContent($content)
    {
        $this->content=$content;
        
        return $this->owner;
    }
    
    /**
     * Загрузка контента страницы
     * 
     * @return Content
     */
    public function loadContent()
    {
        if(!$this->content) {
            if($content=Content::model()->resetScope()->byPageId($this->getId())->find()) {
                $this->setContent($content);
            }
            else {
                $content=new Content;
                $content->setPage($this->owner)->load();
                if($content->save()) {
                    $this->setContent($content);
                }
            }
        }
        
        return $this->getContent();
    }
    
    /**
     * Получить активные страницы
     * 
     * @param Process $process объект текущего процесса
     * 
     * @return Page[]
     */
    public function getActivePages($process)
    {
        $criteria=HDb::criteria(['order'=>'`t`.`status`']);
        $criteria->join='INNER JOIN ' . HDb::qt(Group::model()->tableName()) . ' AS `g` ON (`g`.`id`=`t`.`group_id` AND `g`.`process_id`=:processId)';
        $criteria->params=['processId'=>$process->getId()];
        
        $pages=A::toa(Page::model()->resetScopes()->byStatus([HStatus::NEWEST, HStatus::RUN])->findAll($criteria));
        
        if(!empty($pages)) {
            $groups=Group::model()->resetScopes()->byProcessId($process->getId())->findAll(['index'=>'id']);
            
            foreach($pages as $page) {
                if(!empty($page->group_id) && !empty($groups[$page->group_id])) {
                    $groups[$page->group_id]->setProcess($process);
                    $page->setGroup($groups[$page->group_id]);
                }
            }
        }        
        
        return $pages;
    }
    
    /**
     * Нормализация URL
     * 
     * @param string $url URL
     * 
     * @return string возвращает полный URL,
     * включая домен для относительных ссылок
     */
    public function normalizeUrl($url)
    {
        if(preg_match('#^/[^/].*?$#', $url)) {
            $url=$this->getConfig()->getDomain() . $url;
        }
        
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            $url=null;
        }
        
        return $url;
    }
    
    /**
     * Запуск процесса обработки страницы
     * 
     */
    public function process()
    {
        // @todo добавить ожидание загрузки контента страницы, если сервер не доступен.
        $this->loadContent();
        
        $config=$this->getConfig();
        $groupConfig=$this->getGroup()->getGroupConfig();
        
        // начальная предобработка текста страницы перед запуском парсинга
        if($preContentPattern=$config->getGroupPreContent($groupConfig)) {
            $processedText=$this->processPreContent($this->getContent()->getText(), $preContentPattern);
            if($processedText !== false) {
                $this->getContent()->setText($processedText);
            }
        }
        
        // получением группы конфигураций парсера, которые применяются для данной страницы
        if($this->isPagination()) {
            $groups=[$this->getGroup()];
        }
        else {
            $groups=$this->getGroup()->getSubGroups();
            if($config->isGroupRecursive($groupConfig)) {
                $groups[$this->getGroup()->getId()]=$this->getGroup();
            }
        }
        
        foreach($groups as $group) {
            $this->setGroup($group);
            
            // запуск парсинга получения данных страницы
            $this->processContent();
            
            // запуск парсинга получения ссылок на внутренние страницы
            $this->processLinks();
            
            // запуск парсинга получения ссылок пагинации
            $this->processPagination();
        }
        
        // обработка страницы завершена
        $this->owner->changeStatus(HStatus::DONE);
    }
    
    /**
     * Запукс процесса парсинга данных страницы.
     * Раздел конфигурации "content".
     */
    public function processContent()
    {
        $config=$this->getConfig();
        $groupConfig=$this->getGroup()->getGroupConfig();
                
        $attributes=$config->getGroupContentAttributes($groupConfig);
        if(!empty($attributes)) {
            $text=$this->getContent()->getText();
            
            // начальная предобработка текста перед запуском парсинга контента
            if($preContentPattern=$config->getGroupContentPreContent($groupConfig)) {
                $processedText=$this->processPreContent($text, $preContentPattern);
                if($processedText !== false) {
                    $text=$processedText;
                }
            }
            
            // получение блоков информации для парсинга
            if($blockPattern=$config->getGroupContentPattern($groupConfig)) {
                $items=$this->processPregMatchAllPattern($text, $blockPattern);
                if($items === false) {
                    $items=$this->processCallablePattern($text, $blockPattern);
                    if($items === false) {
                        $items=$this->processDomNodesPattern($text, $blockPattern);
                    }
                }
            }
            
            // заполнение данных для сохранения
            if(!empty($items)) {
                $data=[];
                foreach($items as $itemText) {
                    $itemAttributes=$this->processContentAttributes($itemText, $attributes);
                    if($this->checkContentRequireAttributes($itemAttributes)) {
                        $data[]=$itemAttributes;
                    }
                }
            }
            
            if(!empty($data)) {
                // сохранение данных                
                if($hBeforeSave=$config->getGroupContentBeforeSaveHandler($groupConfig)) {
                    $data=call_user_func_array($hBeforeSave, [$this->owner, &$data]);
                }
                
                if($hSave=$config->getGroupContentSaveHandler($groupConfig)) {
                    call_user_func_array($hSave, [$this->owner, $data]);
                }
                elseif($hSave=$config->getSaveHandler($groupConfig)) {
                    call_user_func_array($hSave, [$this->owner, $data]);
                }
                else {
                    $this->saveData($data);
                }
            }
        }
    }
    
    /**
     * Запуск процесса парсинга ссылок на внутренние страницы.
     * Раздел конфигурации "links".
     */
    public function processLinks()
    {
        $config=$this->getConfig();
        $groupConfig=$this->getGroup()->getGroupConfig();
        
        if($config->getGroupLinks($groupConfig)) {
            $text=$this->getContent()->getText();
        
            // начальная предобработка текста перед запуском парсинга контента
            if($preContentPattern=$config->getGroupLinksPreContent($groupConfig)) {
                $processedText=$this->processPreContent($text, $preContentPattern);
                if($processedText !== false) {
                    $text=$processedText;
                }
            }
            
            // обработка ссылок
            $links=[];
            if($linksPattern=$config->getGroupLinksPattern($groupConfig)) {
                $links=$this->processPregMatchAllPattern($text, $linksPattern);
                if($links === false) {
                    $links=$this->processCallablePattern($text, $linksPattern);
                    if($links === false) {
                        $links=$this->processDomAttributesPattern($text, $linksPattern, 'href');
                    }
                }
            }
            
            if(!empty($links)) {
                // установка основных атрибутов
                foreach($links as $idx=>$link) {
                    $links[$idx]=[];
                    $links[$idx]['url']=$this->normalizeUrl($link);
                    $links[$idx]['process_id']=$this->getGroup()->getProcess()->getId();
                    $links[$idx]['group_id']=$this->getGroup()->getId();
                    $links[$idx]['hash']=$this->generateHash(A::get($links[$idx], 'url', ''));
                    $links[$idx]['create_time']=new \CDbExpression('NOW()');
                    $links[$idx]['status']=HStatus::NEWEST;
                    $links[$idx]['type']=Page::TYPE_LINK;
                    $links[$idx]['parent_page_id']=$this->getId();
                }
                
                // сохранение ссылок
                if($hBeforeSave=$config->getGroupLinksBeforeSaveHandler($groupConfig)) {
                    $links=call_user_func_array($hBeforeSave, [$this->owner, &$links]);
                }
                
                if($hSave=$config->getGroupLinksSaveHandler($groupConfig)) {
                    call_user_func_array($hSave, [$this->owner, $links]);
                }
                elseif($hSave=$config->getSaveHandler($groupConfig)) {
                    call_user_func_array($hSave, [$this->owner, $links]);
                }
                else {
                    $this->saveLinks($links);
                }
            }
        }
    }
    
    /**
     * Запуск процесс парсинга ссылок пагинатора.
     * Раздел конфигурации "pagination".
     */
    public function processPagination()
    {
        $config=$this->getConfig();
        $groupConfig=$this->getGroup()->getGroupConfig();
        
        if($config->getGroupPagination($groupConfig)) {
            $text=$this->getContent()->getText();
            
            // начальная предобработка текста перед запуском парсинга контента
            if($preContentPattern=$config->getGroupPaginationPreContent($groupConfig)) {
                $processedText=$this->processPreContent($text, $preContentPattern);
                if($processedText !== false) {
                    $text=$processedText;
                }
            }
            
            // обработка ссылок пагинатора
            $links=[];
            if($linksPattern=$config->getGroupPaginationPattern($groupConfig)) {
                $links=$this->processPregMatchAllPattern($text, $linksPattern);
                if($links === false) {
                    $links=$this->processCallablePattern($text, $linksPattern);
                    if($links === false) {
                        $links=$this->processDomAttributesPattern($text, $linksPattern, 'href');
                    }
                }
            }
            
            if(!empty($links)) {
                // установка основных атрибутов
                foreach($links as $idx=>$link) {
                    $links[$idx]=[];
                    $links[$idx]['url']=$this->normalizeUrl($link);
                    $links[$idx]['process_id']=$this->getGroup()->getProcess()->getId();
                    $links[$idx]['group_id']=$this->getGroup()->getId();
                    $links[$idx]['hash']=$this->generateHash(A::get($links[$idx], 'url', ''));
                    $links[$idx]['create_time']=new \CDbExpression('NOW()');
                    $links[$idx]['status']=HStatus::NEWEST;
                    $links[$idx]['type']=Page::TYPE_PAGINATION;
                    $links[$idx]['parent_page_id']=$this->getId();
                }
                
                // сохранение ссылок
                if($hBeforeSave=$config->getGroupPaginationBeforeSaveHandler($groupConfig)) {
                    $links=call_user_func_array($hBeforeSave, [$this->owner, &$links]);
                }
                
                if($hSave=$config->getGroupPaginationSaveHandler($groupConfig)) {
                    call_user_func_array($hSave, [$this->owner, $links]);
                }
                elseif($hSave=$config->getSaveHandler($groupConfig)) {
                    call_user_func_array($hSave, [$this->owner, $links]);
                }
                else {
                    $this->savePaginationLinks($links);
                }
            }
        }
    }
    
    /**
     * Создание уникальных ключей для обновления данных
     */
    public function createContentSyncAttribute()
    {
        $config=$this->getConfig();
        $groupConfig=$this->getGroup()->getGroupConfig();
        
        if($syncAttribute=$config->getGroupContentSyncAttribute($groupConfig)) {
            if(!isset(static::$syncColumnExists[$syncAttribute])) {
                static::$syncColumnExists[$syncAttribute]=false;
                if($tableName=$config->getGroupContentTableName($groupConfig)) {
                    if($table=HDb::getTable($tableName, true)) {
                        if(!$table->getColumn($syncAttribute)) {
                            HDb::execute(HDb::schema()->addColumn($tableName, $syncAttribute, 'VARCHAR(32)'));
                            HDb::execute(HDb::schema()->createIndex("unq_{$syncAttribute}", $tableName, $syncAttribute, true));
                        }
                        static::$syncColumnExists[$syncAttribute]=true;
                    }
                }
            }
        }
        
        if(!$syncAttribute || empty(static::$syncColumnExists[$syncAttribute])) {
            throw new PageException('Атрибут синхронизации данных не найден.');
        }
    }
    
    /**
     * Установить для данных значение хэша синхронизации
     * 
     * @param [] $data массив элементов для сохранения, где каждый элемент это массив атрибутов вида array(attribute=>value).
     */
    public function setDataSyncHashAttribute(&$data)
    {
        $config=$this->getConfig();
        $groupConfig=$this->getGroup()->getGroupConfig();
        
        // создаем поле хэша синхронизации
        $this->createContentSyncAttribute();
        
        $syncAttribute=$config->getGroupContentSyncAttribute($groupConfig);
        
        // заполняем значение поля хэша синхронизации
        $hashes=[];
        foreach($data as $idx=>$item) {
            $syncHash=$this->getSyncHash($item);
            if(isset($hashes[$syncHash])) {
                // убираем дубликаты записей
                unset($data[$idx]);
            }
            else {
                $data[$idx][$syncAttribute]=$syncHash;
                $hashes[$syncHash]=true;
            }
        }
    }
    
    /**
     * Получить SQL выражение для ON DUBLICATE UPDATE для секции "content"
     * 
     * @return string
     */
    public function getContentOnDublicateSQL()
    {
        $config=$this->getConfig();
        $groupConfig=$this->getGroup()->getGroupConfig();
        
        $onDublicateSQL=$config->getGroupContentOnDublicateSQL($groupConfig);
        if($onDublicateSQL === null) {
            $syncAttribute=$config->getGroupContentSyncAttribute($groupConfig);
            $onDublicateSQL=HDb::qc($syncAttribute) . '=' . HDb::qc($syncAttribute);
        }
        
        return $onDublicateSQL;
    }
    
    /**
     * Сохранение данных в базу данных
     * @param array $data массив элементов для сохранения, где каждый элемент это массив атрибутов вида array(attribute=>value).
     * @return integer|false возвращает количество обработанных записей, либо false в случае ошибки.
     */
    protected function saveData($data)
    {
        $config=$this->getConfig();
        $groupConfig=$this->getGroup()->getGroupConfig();
        
        if($tableName=$config->getGroupContentTableName($groupConfig)) {
            $this->setDataSyncHashAttribute($data);
            
            // сохраняем данные в базу данных            
            return HDb::insert($tableName, $data, $this->getContentOnDublicateSQL());
        }
        
        return false;
    }
        
    /**
     * Сохранение ссылок
     * @param array $links массив ссылок для сохранения, где каждая ссылка определяется 
     * как массив атрибутов ссылки вида array(attribute=>value).
     * @return integer|false возвращает количество обработанных записей, либо false в случае ошибки.
     */
    protected function saveLinks($links)
    {
        return HDb::insert($this->owner->tableName(), $links, '`create_time`=NOW()');
    }
    
    /**
     * Сохранение ссылок пагинатора
     * @param array $links массив ссылок пагинатора для сохранения, где каждая ссылка определяется 
     * как массив атрибутов ссылки вида array(attribute=>value).
     * @return integer|false возвращает количество обработанных записей, либо false в случае ошибки.
     */
    protected function savePaginationLinks($links)
    {
        return HDb::insert($this->owner->tableName(), $links, '`create_time`=NOW()');
    }
    
    /**
     * Проверка заполненности обязательных атрибутов
     * @param array $attributes массив атрибутов вида array(attribute=>value)
     * @return bool возвращает false если обязательный атрибут не задан 
     */
    protected function checkContentRequireAttributes($attributes)
    {
        if($requireds=$this->getConfig()->getGroupContentRequired($this->getGroup()->getGroupConfig())) {
            foreach($requireds as $attribute) {
                if(!A::existsKey($attributes, $attribute) 
                    || (!is_numeric($attributes[$attribute]) && empty($attributes[$attribute]))) 
                {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Запуск предварительной обработки контента страницы
     * @param string $text текст к которому применяется обработка. 
     * @param array|callable $pattern паттерн предварительной обработки контента страницы
     * @return string|false результат обработки. Возвращает false если предварительная 
     * обработка не требуется.
     */
    protected function processPreContent($text, $pattern)
    {
        $processedText=$this->processCallablePattern($text, $pattern);
        if($processedText === false) {
            $processedText=$this->processPregReplacePattern($text, $pattern);
            if($processedText === false) {
                $processedText=$this->processDomNodePattern($text, $pattern);
            }
        }
        
        return $processedText;
    }
    
    /**
     * Запуск парсинга данных (атрибутов) для сохранения в базу данных
     * @param string $text текст к которому применяется обработка. 
     * @param array|callable $attributes шаблоны для получения значений полей элемента.
     * Варианты принимаемых значений:
     *  (callable) функция вида function($blockContent) { return array }
     *   param $blockContent (string) контент блока
     *   return array функция должна возвращать массив атрибутов со значениями вида array(attribute=>string).
     *   
     *  (array) массив вида array(attribute => value), где 
     *   "attribute" - (string) имя поля в таблице базы данных
     *   "value" - (string|array|callable) паттерн получения значения атрибута.
     *   Варианты принимаемых значений параметра "value":
     *      - (string) паттерн для функции preg_match(), значение будет получено из $matches[1];
     *                   
     *      - (array) может быть передан массив array("preg_replace", pattern, replace)
     *          pattern (string) паттерн для функции preg_replace();
     *          replace (string|callback) параметр repalce для функции preg_replace();
     *              Если передан callback, то будет возвращен результат функции preg_replace_callback();
     *           
     *      - (array) может быть передан массив array("dom", find_selector, attribute)
     *          find_selector (string) подробнее \simple_html_dom_node->find();
     *          attribute (string) имя атрибута DOM элемента, если в качестве значения необходимо получить его содержимое.
     *              Если не передано, то в качестве значения будет получено внутреннее содержимое элемента.
     *                       
     *      - (callable) функция вида function($blockContent) { return string }
     *          param $blockContent (string) контент блока
     *          return string значение атрибута             
     *  
     * @return array массив атрибутов со значениями вида array(attribute=>string).  
     */
    protected function processContentAttributes($text, $attributes)
    {
        $config=$this->getConfig();
        $groupConfig=$this->getGroup()->getGroupConfig();
        
        $data=[];
        
        if(!is_string($attributes) && is_callable($attributes)) {
            $data=call_user_func_array($attributes, [$this->owner, $text]);
        }
        elseif(is_array($attributes)) {
            foreach($attributes as $attribute=>$value) {
                if($value instanceof \CDbExpression) {
                    $data[$attribute]=$value;
                }
                else {
                    if(is_string($value)) {
                        $data[$attribute]=$this->processPregMatchPattern($text, $value);
                    }
                    elseif(is_array($value)) {
                        $data[$attribute]=$this->processPregReplacePattern($text, $value);
                        if($data[$attribute] === false) {
                            $data[$attribute]=$this->processDomAttributePattern($text, $value);
                        }
                    }
                    elseif(is_callable($value)) {
                        $data[$attribute]=call_user_func_array($value, [$this->owner, $text]);
                    }
                    $data[$attribute]=trim((string)$data[$attribute]);
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Запуск обработки текста паттерном типа callable
     * 
     * @param string $text текст к которому применяется паттерн. 
     * @param callable $pattern паттерн. 
     * @param array $params дополнительные аргументы для callable функции.
     * Первым агрументов всегда будет текст, к которому применяется паттерн.
     * 
     * @return string|false результат применения паттерна. Если передан 
     * некорректный паттерн будет возвращено false.
     */
    public function processCallablePattern($text, $pattern, $params=[])
    {
        if(!is_string($pattern) && is_callable($pattern, true) && (!empty($pattern[0]) && !in_array($pattern[0], ['preg_match', 'preg_replace', 'dom']))) {
            array_unshift($params, $text);
            array_unshift($params, $this->owner);
            
            return call_user_func_array($pattern, $params);
        }
        
        return false;
    }
    
    /**
     * Запуск обработки текста паттерном "preg_replace".
     * 
     * @param string $text текст к которому применяется паттерн. 
     * @param array $pattern паттерн. 
     * Паттерн "preg_replace" задается как массив вида array("preg_replace", pattern, replace), 
     * где:
     *  "preg_replace" - ключевое слово, указывающее на то, что это паттерн "preg_replace".
     *  "pattern" (string) паттерн для функции preg_replace();
     *  "replace" (string|callback) параметр repalce для функции preg_replace(). Если передан 
     *  тип callback, то будет возвращен результат функции preg_replace_callback();
     *  
     * @return string|false результат применения паттерна. Если передан 
     * некорректный паттерн будет возвращено false.
     */
    public function processPregReplacePattern($text, $pattern)
    {
        if(is_array($pattern) && (count($pattern) === 3) && ($pattern[0] === 'preg_replace')) {
            if(!is_string($pattern[2]) && is_callable($pattern[2])) {
                return preg_replace_callback($pattern[1], $pattern[2], $text);
            }
            else {
                return preg_replace($pattern[1], $pattern[2], $text);
            }
        }
        
        return false;
    }
    
    /**
     * Запуск обработки текста паттерном "dom".
     * 
     * @param string $text текст к которому применяется паттерн.
     * @param array $pattern паттерн. 
     * Паттерн "dom" задается как массив вида array("dom", find_selector), 
     * где:
     *  "dom" - ключевое слово, указывающее на то, что это паттерн "dom".
     *  "find_selector" (string) подробнее \simple_html_dom_node->find();
     * @param int|null $idx индекс получаемого элемента.  
     * По умолчанию (null) будут возвращены значения для всех найденных элементов.
     * @param string $attribute имя атрибута DOM элемента из которого получить 
     * значение для результата. Доступны также "innertext", "outertext", "plaintext".
     */
    public function processDomPattern($text, $pattern, $attribute, $idx=null)
    {
        if(is_array($pattern) && (count($pattern) > 1) && ($pattern[0] === 'dom')) {
            if($dom=HDom::get($text)) {
                if($idx === null) {
                    if($nodes=$dom->find($pattern[1])) {
                        $items=[];                    
                        foreach($nodes as $node) {
                            $items[]=$node->{$attribute};
                        }
                        
                        return $items;
                    }
                }
                elseif($node=$dom->find($pattern[1], $idx)) {
                    return $node->{$attribute};
                }
            }
            
            return '';
        }
        
        return false;
    }
    
    /**
     * Запуск обработки текста паттерном "dom".
     * 
     * Применяется для получения содержимого только первого DOM элемента.
     * 
     * @param string $text текст к которому применяется паттерн. 
     * @param array $pattern паттерн. 
     * Паттерн "dom" задается как массив вида array("dom", find_selector, inner), 
     * где:
     *  "dom" - ключевое слово, указывающее на то, что это паттерн "dom".
     *  "find_selector" (string) подробнее \simple_html_dom_node->find();
     *  Будет возвращен только первый найденный элемент с индексом 0 (нуль).
     *  "inner" (bool) получить только внутреннее содержимое. 
     *  По умолчанию (false) будет возвращено содержимое вместе с родительским элементом (outertext).
     *  
     * @return string|false результат применения паттерна.
     * Если передан некорректный паттерн будет возвращено false.
     */
    public function processDomNodePattern($text, $pattern)
    {
        $attribute=empty($pattern[2]) ? 'outertext' : 'innertext';
        
        return $this->processDomPattern($text, $pattern, $attribute, 0);
    }
    
    /**
     * Запуск обработки текста паттерном "dom".
     * 
     * Применяется для получения содержимого всех найденных DOM элементов.
     *
     * @param string $text текст к которому применяется паттерн.
     * @param array $pattern паттерн.
     * Паттерн "dom" задается как массив вида array("dom", find_selector, inner),
     * где:
     *  "dom" - ключевое слово, указывающее на то, что это паттерн "dom".
     *  "find_selector" (string) подробнее \simple_html_dom_node->find();
     *  Будет возвращен только первый найденный элемент с индексом 0 (нуль).
     *  "inner" (bool) получить только внутреннее содержимое.
     *  По умолчанию (false) будет возвращено содержимое вместе с родительским элементом (outertext).
     *
     * @return array|false результат применения паттерна.
     * Если передан некорректный паттерн или DOM-элемент не найден будет возвращено false.
     */
    public function processDomNodesPattern($text, $pattern)
    {
        $attribute=empty($pattern[2]) ? 'outertext' : 'innertext';
        
        return $this->processDomPattern($text, $pattern, $attribute);
    }
    
    /**
     * Запуск обработки текста паттерном "dom".
     *
     * Применяется для получения содержимого значения атрибута только первого DOM элемента.
     *
     * @param string $text текст к которому применяется паттерн.
     * @param array $pattern паттерн.
     * Паттерн "dom" задается как массив вида array("dom", find_selector, attribute),
     * где:
     *  "dom" - ключевое слово, указывающее на то, что это паттерн "dom".
     *  "find_selector" (string) подробнее \simple_html_dom_node->find();
     *  Будет возвращен только первый найденный элемент с индексом 0 (нуль).
     *  "attribute" string|null|true имя атрибута. 
     *  Если передано строгое true будет возвращено внутреннее содержимое элемента (plaintext).
     *  По умолчанию (null) будет возвращено внутреннее содержимое элемента (innertext).
     * @param string|null $defaultAttribute имя атриубта по умолчанию. 
     * По умолчанию (null) не задан.
     * @return string|false результат применения паттерна.
     * Если передан некорректный паттерн будет возвращено false.
     */
    public function processDomAttributePattern($text, $pattern, $defaultAttribute=null)
    {
        if(empty($pattern[2])) {
            $attribute='innertext';
        }
        elseif($pattern[2] === true) {
            $attribute='plaintext';
        }
        elseif(is_string($pattern[2])) {
            $attribute=$pattern[2];
        }
        elseif($defaultAttribute) {
            $attribute=$defaultAttribute;
        }
        else {
            return false;
        }
        
        return $this->processDomPattern($text, $pattern, $attribute, 0);
    }
    
    /**
     * Запуск обработки текста паттерном "dom".
     *
     * Применяется для получения содержимого значения атрибута для всех найденых DOM элементов.
     *
     * @param string $text текст к которому применяется паттерн.
     * @param array $pattern паттерн.
     * Паттерн "dom" задается как массив вида array("dom", find_selector, attribute),
     * где:
     *  "dom" - ключевое слово, указывающее на то, что это паттерн "dom".
     *  "find_selector" (string) подробнее \simple_html_dom_node->find();
     *  Будет возвращен только первый найденный элемент с индексом 0 (нуль).
     *  "attribute" string|null|true имя атрибута.
     *  Если передано строгое true будет возвращено внутреннее содержимое элемента (plaintext).
     *  По умолчанию (null) будет возвращено внутреннее содержимое элемента (innertext).
     * @param string|null $defaultAttribute имя атриубта по умолчанию. 
     * По умолчанию (null) не задан.
     * @return array|false результат применения паттерна.
     * Если передан некорректный паттерн будет возвращено false.
     */
    public function processDomAttributesPattern($text, $pattern, $defaultAttribute=null)
    {
        if(empty($pattern[2])) {
            $attribute=$defaultAttribute ?: 'innertext';
        }
        elseif($pattern[2] === true) {
            $attribute='plaintext';
        }
        elseif(is_string($pattern[2])) {
            $attribute=$pattern[2];
        }
        elseif($defaultAttribute) {
            $attribute=$defaultAttribute;
        }
        else {
            return false;
        }
        
        return $this->processDomPattern($text, $pattern, $attribute);
    }
    
    /**
     * Запуск обработки текста паттерном "preg_match_all".
     *
     * @param string $pattern паттерн для функции preg_match_all()
     * @param int $flags флаги для функции preg_match_all(). По умолчанию PREG_PATTERN_ORDER.
     *
     * @return array|false результат применения паттерна.
     * Будет возвращены значения из $matches[1]. 
     * Если передан некорректный паттерн будет возвращено false.
     */
    public function processPregMatchAllPattern($text, $pattern, $flags=PREG_PATTERN_ORDER)
    {
        if(is_string($pattern)) {
            $matches=[];            
            if(preg_match_all($pattern, $text, $matches, $flags)) {
                if(!empty($matches[1])) {
                    return $matches[1];
                }
            }
            
            return [];
        }
        
        return false;
    }
    
    /**
     * Запуск обработки текста паттерном "preg_match".
     *
     * @param string $pattern паттерн для функции preg_match()
     *
     * @return array|false результат применения паттерна.
     * Будет возвращено значение из $matches[1]. 
     * Если передан некорректный паттерн будет возвращено false.
     */
    public function processPregMatchPattern($text, $pattern)
    {
        if(is_string($pattern)) {
            $matches=[];            
            if(preg_match($pattern, $text, $matches)) {
                if(!empty($matches[1])) {
                    return trim($matches[1]);
                }
            }
            
            return '';
        }
        
        return false;
    }
}