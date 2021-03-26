<?php
namespace common\ext\parser\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\ext\parser\components\helpers\HStatus;
use common\ext\parser\components\exceptions\GroupException;
use crud\models\ar\common\ext\parser\models\Process;
use crud\models\ar\common\ext\parser\models\Group;
use crud\models\ar\common\ext\parser\models\Page;

class GroupModelBehavior extends \CBehavior
{
    /**
     * Объект процесса парсинга
     * @var Process
     */
    private $process;
    
    /**
     * Объект родительской группы
     * @var Group
     */
    private $parentGroup;
    
    /**
     * Scope: по идентификатору процесса
     * 
     * @param integer $id идентификатор процесса
     * 
     * @return Group
     */
    public function byProcessId($id)
    {
        $criteria=new \CDbCriteria();
        
        $criteria->addColumnCondition(['process_id'=>$id]);
        
        $this->owner->getDbCriteria()->mergeWith($criteria);
        
        return $this->owner;
    }
    
    /**
     * Scope: по текущему процессу
     * 
     * @param integer $id идентификатор процесса
     * 
     * @return Group
     */
    public function byProcess()
    {
        return $this->byProcessId($this->getProcess()->getId());
    }
    
    /**
     * Scope: по идентификатору родительской группы
     * 
     * @param integer $id идентификатор родительской группы
     * 
     * @return Group
     */
    public function byParentGroupId($id)
    {
        $criteria=new \CDbCriteria();
        
        $criteria->addColumnCondition(['parent_group_id'=>$id]);
        
        $this->owner->getDbCriteria()->mergeWith($criteria);
        
        return $this->owner;
    }
    
    /**
     * Scope: по родительской группы
     * 
     * @param Group|null родительская группа. 
     * Если не задано будет установлено условие получения всех 
     * корневых групп.
     * 
     * @return Group
     */
    public function byParentGroup($parentGroup=null)
    {
        $parentGroupId=0;
        
        if($parentGroup instanceof Group) {
            $parentGroupId=$parentGroup->getId();
        }
        
        return $this->byParentGroupId($parentGroupId);
    }
    
    /**
     * Получить объект процесса парсера
     * 
     * @throws GroupException 
     * @return Process
     */
    public function getProcess()
    {
        if(!($this->process instanceof Process)) {
            throw new GroupException('Процесс не найден.');
        }
        
        return $this->process;
    }
    
    /**
     * Установить объект текущего процесса
     * 
     * @param Process $process объект текущего процесса
     * 
     * @return Group
     */
    public function setProcess($process)
    {
        $this->process=$process;
        
        if($this->process) {
            $this->owner->process_id=$this->getProcess()->getId();
        }
        
        return $this->owner;
    }
    
    /**
     * Группа является входной группой
     * 
     * @return boolean
     */
    public function isEntryGroup()
    {
        return !$this->code;
    }
    
    /**
     * Получить идентификатор группы
     * 
     * @return int
     */
    public function getId()
    {
        return (int)$this->owner->id;
    }
    
    /**
     * Получить идентификатор родительской группы
     * 
     * @return int
     */
    public function getParentGroupId()
    {
        return (int)$this->owner->parent_group_id;
    }
    
    /**
     * Получить символьный код группы
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->owner->code;
    }
    
    /**
     * Установить символьный код группы
     * 
     * @param string $code символьный код группы
     * 
     * @return Group
     */
    public function setCode($code)
    {
        $this->owner->code=$code;
        
        return $this->owner;
    }
    
    /**
     * Получить конфигурацию парсера
     * 
     * @return \common\ext\parser\models\Config
     */
    public function getConfig()
    {
        return $this->getProcess()->getConfig();
    }
    
    /**
     * Получить конфигурацию группы
     * 
     * @return []
     */
    public function getGroupConfig()
    {
        return $this->getConfig()->getGroupsByPath($this->getGroupPath());
    }
    
    /**
     * Получить конфигурацию подгрупп
     * 
     * @return []
     */
    public function getSubGroupsConfig()
    {
        if($this->isEntryGroup()) {
            return $this->getConfig()->getGroups();
        }
        else {
            return $this->getConfig()->getSubGroupsByPath($this->getGroupPath());
        }
    }
    
    /**
     * Получить подгруппы
     * 
     * @return Group[]
     */
    public function getSubGroups()
    {
        $subGroups=$this->byParentGroupId($this->getId())->findAll(['index'=>'id']);
        
        if(!$subGroups) {
            $subGroups=[];
        }
        
        foreach($subGroups as $id=>$subGroup) {
            $subGroups[$id]->setProcess($this->getProcess());
        }
            
        return $subGroups;
    }
    
    /**
     * Получить полный путь к группе, включая символьный код самой группы. 
     * 
     * @return string
     */
    public function getGroupPath()
    {
        $path=$this->owner->path;
        
        return $path . ($path ? '.' : '') . $this->owner->code;
    }
    
    /**
     * Получить объект родительской группы
     * 
     * @return Group|null
     */
    public function getParentGroup()
    {
        if(!$this->parentGroup && $this->owner->parent_group_id) {
            $group=new Group;        
            if($parentGroup=$group->findByPk($this->getParentGroupId())) {
                $parentGroup->setProcess($this->getProcess());
                
                $this->parentGroup=$parentGroup;
                
                return $parentGroup;
            }
        }
        
        return $this->parentGroup;
    }
    
    /**
     * Установить родительскую группу
     * 
     * @param Group|null $parentGroup родительская группа
     * 
     * @return Group
     */
    public function setParentGroup($parentGroup=null)
    {
        if($parentGroup instanceof Group) {
            $this->parentGroup=$parentGroup;
            
            $this->owner->parent_group_id=$parentGroup->getId();
            $this->owner->path=$parentGroup->getGroupPath();
            $this->owner->depth=(int)$parentGroup->depth + 1;            
        }
        else {
            $this->parentGroup=null;
            
            $this->owner->parent_group_id=0;
            $this->owner->path='';
            $this->owner->depth=0;
        }
        
        return $this->owner;
    }
    
    /**
     * Добавить подгруппы
     * 
     * @param bool $recursive добавить подгруппы рекурсивно.
     * По умолчанию (false) будут добавлены только прямые подгруппы.
     * 
     * @return Group[] массив объектов добавленных подгрупп.
     */
    public function addSubGroups($recursive=false)
    {
        $subGroups=[];
        
        $groups=$this->getSubGroupsConfig();
        
        foreach($groups as $code=>$groupConfig) {
            $group=new Group;
            $group->setCode($code);
            $group->setProcess($this->getProcess());
            $group->setParentGroup($this->owner);
            
            if($group->save()) {
                $subGroups[]=$group;
                if($recursive) {
                    $subSubGroups=$group->addSubGroups(true);
                    foreach($subSubGroups as $subSubGroup) {
                        $subGroups[]=$subSubGroup;
                    }
                }
            }
        }
        
        return $subGroups;
    }
    
    /**
     * Создать основную группу для входной страницы
     * @return Group группа входной страницы
     */
    public function createEntryGroup()
    {
        // создаем входную группу
        $entryGroup=new Group;        
        $entryGroup->setCode('');
        $entryGroup->setProcess($this->getProcess());
        $entryGroup->setParentGroup();
        
        if(!$entryGroup->save()) {
            throw new ProcessException('Не удалось создать группу для входной страницы.');
        }
        
        // добавляем все подгруппы из конфигурации
        $entryGroup->addSubGroups(true);
        
        // добавляем входную страницу
        $page=new Page;
        $page->setGroup($entryGroup);
        $page->setUrl($this->getConfig()->getEntry());
        $page->setType(Page::TYPE_ENTRY);
        
        if(!$page->save()) {
            throw new GroupException('Не удалось создать входную страницу.');
        }
        
        return $entryGroup;
    }
}