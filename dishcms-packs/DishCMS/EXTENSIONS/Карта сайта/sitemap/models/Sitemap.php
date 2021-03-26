<?php

namespace seo\ext\sitemap\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HDb;
use common\components\helpers\HHash;
use seo\ext\sitemap\components\Sitemap as SitemapComponent;
use seo\ext\sitemap\components\helpers\HSitemap;


class Sitemap 
{
    const LIMIT=100;
    const ROOT='root';

    private $config=[];
    private $percent=-1;
    private $offset=0;
    private $limit=100;
    private $sectionPath='';
    private $totalCount=null;
    private $processedCount=0;
    private $totalProcessedCount=0;
    private $sitemapIndexPrefix='';
    private $errors=[];

    /**
     * Получение дополнительного ключа шифрования
     * @return string
     */
    public static function secure()
    {
        return '';
    }

    /**
     * Расшифровать значение
     *
     * @param \common\ext\iterator\models\Process $iteratorProcess
     * @param string $text зашифрованное значение
     * @return string расшифрованное значение
     */
    public static function decryptValue($iteratorProcess, $text)
    {
        return HHash::srDecrypt($text, md5($iteratorProcess->getConfig()->getPath() . '.' . $iteratorProcess->getConfig()->getId()));
    }

    /**
     * Создание процесса генерации карты сайта
     * @param \common\ext\iterator\models\Process $iteratorProcess
     * @return []
     */
    public static function create($iteratorProcess)
    {
        if($configName=R::post('config')) {
            $configName=static::decryptValue($iteratorProcess, $configName);
            if(HSitemap::loadConfig($configName)) {
                return [
                    'config'=>$configName, 
                    // limit - кол-во обрабатываемых страниц за итерацию
                    'limit'=>R::post('limit', self::LIMIT)
                ];
            }
        }
        
        $iteratorProcess->addError('Некорректный запрос');
        
        R::e400();
    }
    
    /**
     * Итерация генерации карты сайта
     * @param \common\ext\iterator\models\Process $iteratorProcess
     * @return int процент завершенности
     */
    public static function next($iteratorProcess)
    {
        $configName=$iteratorProcess->getDataParam('config');
        if($config=HSitemap::loadConfig($configName)) {
            try {
                $sitemap=new static;
                $sitemap->setConfig($config);
                $sitemap->setLimit($iteratorProcess->getDataParam('limit', self::LIMIT));
                $sitemap->setOffset($iteratorProcess->getParam('offset', 0));
                $sitemap->setActiveSectionPath($iteratorProcess->getParam('section', ''));
                $sitemap->setTotalProcessedCount($iteratorProcess->getParam('processed', 0));
                $sitemap->setTotalCount($iteratorProcess->getParam('total', null));

                $sitemap->generate();

                if($sitemap->hasErrors()) {
                    $iteratorProcess->addError(nl2br($sitemap->getErrors(true)));
                    $iteratorProcess->setPercent(-1);
                }
                else {
                    $iteratorProcess->setParam('total', $sitemap->getTotalCount());
                    $iteratorProcess->setParam('processed', $sitemap->getTotalProcessedCount());
                    $iteratorProcess->setParam('section', $sitemap->getActiveSectionPath());
                    $iteratorProcess->setParam('offset', $sitemap->getOffset());
                    $iteratorProcess->setParam('message', $sitemap->getStatusMessage());
                    $iteratorProcess->setPercent($sitemap->getPercent());
                }
            }
            catch(\Throwable $e) {
                $iteratorProcess->addError($e->getMessage());
                $iteratorProcess->setPercent(-1);
            }
                
            return $iteratorProcess->getPercent();
        }
        else {
            $iteratorProcess->addError('Некорректный запрос');
        }
        
        return -1;
    }

    /**
     * Получить сообщение текущего состояния процесса 
     * генерации карты сайта.
     *
     * @return string
     */
    public function getStatusMessage()
    {
        if($this->getPercent() == 100) {
            $message='Генерация карты сайта успешно завершена';
        }
        else {
            $section=$this->getActiveSection();
            $message='Обработано записей ' . $this->getTotalProcessedCount() . ' из ' . $this->getTotalCount();
            if($title=HSitemap::getCallableValue(A::get($section, 'title'))) {
                $message.="<br/><small style=\"opacity:0.7\"><span>Идет обработка раздела:</span> {$title}</small>";
            }
        }

        return $message;
    }

    /**
     * Сбрасывает ошибки
     *
     * @return void
     */
    public function resetErrors()
    {
        $this->errors=[];
    }

    /**
     * Получить список ошибок
     *
     * @param bool $returnString возвращать ошибки строкой.
     * @return []|string
     */
    public function getErrors($returnString=false)
    {
        if($returnString) {
            return implode("\n", $this->errors);
        }

        return $this->errors;
    }

    /**
     * Проверяет есть ли ошибки
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Добавить ошибку
     *
     * @param string $message сообщение об ошибке
     * @return void
     */
    public function addError($message)
    {
        $this->errors[]=$message;
    }

    /**
     * Получить текущую конфигурацию карты сайта.
     *
     * @return []
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Установить текущую конфигурацию карты сайта.
     *
     * @param [] $config конфигурация карты сайта.
     * @return void
     */
    public function setConfig($config)
    {
        $this->config=[self::ROOT=>$config];
    }

    /**
     * Получить процент завершенности
     *
     * @return int
     */
    public function getPercent()
    {
        return (int)$this->percent;
    }

    /**
     * Установить процент завершенности
     *
     * @param number $percent процент завершенности
     * @return void
     */
    public function setPercent($percent)
    {
        if($percent > 100) {
            $percent=100;
        }

        $this->percent=$percent;
    }

    /**
     * Вычисление текущего процента завершенности
     * процесса генерации карты сайта.
     * 
     * После вычисления процента будет выполнена
     * запись значения Sitemap::setPercent()
     * 
     * @return int процент завершенности
     */
    public function calcPercent()
    {
        $this->setPercent($this->hasErrors() ? -1 : round(($this->getTotalProcessedCount() * 100) / $this->getTotalCount()));

        return $this->getPercent();
    }

    /**
     * Получить кол-во элементов в разделе
     *
     * @param [] $section раздел, для которого вычисляется кол-во.
     * @param bool $withSubsections включая подразделы. 
     * По умолчанию (false) не получать кол-во из подразделов.
     * @return void
     */
    public function getSectionItemCount($section, $withSubsections=false)
    {
        $count=0;

        if(!HSitemap::getCallableValue(A::get($section, 'disabled', false))) {
            if($className=A::get($section, 'class')) {
                $criteria=HSitemap::getCallableValue(A::get($section, 'criteria', []), []);
                $criteria=HDb::criteria($criteria);
                $count=$className::model()->count($criteria);
            }
            else {
                $count=HSitemap::getCallableValue(A::get($section, 'loc'), [null]) ? 1 : 0;
            }

            if($withSubsections) {
                foreach(A::get($section, 'items', []) as $name=>$subsection) {
                    $count+=$this->getSectionItemCount($subsection, true);
                }
            }
        }

        return $count;
    }

    /**
     * Получить кол-во уже обработанных записей 
     * текущего раздела
     *
     * @return int
     */
    public function getOffset()
    {
        return (int)$this->offset;
    }

    /**
     * Установить кол-во уже обработанных записей 
     * текущего раздела
     *
     * @param number $offset кол-во уже обработанных записей 
     * текущего раздела.
     * @return void
     */
    public function setOffset($offset)
    {
        $this->offset=(int)$offset;
    }

    /**
     * Получить максимальное кол-во обрабатываемых 
     * записей за итерацию.
     *
     * @return int
     */
    public function getLimit()
    {
        return (int)$this->limit;
    }

    /**
     * Установить максимальное кол-во обрабатываемых 
     * записей за итерацию.
     *
     * @param number $limit
     * @return void
     */
    public function setLimit($limit)
    {
        $this->limit=(int)$limit;
    }

    /**
     * Получить конфигурацию текущего раздела
     *
     * @return []
     */
    public function getActiveSection()
    {
        return A::rget($this->getConfig(), $this->getActiveSectionFullPath(), []);
    }

    /**
     * Получить сокращенный путь к текущему обрабатываемому разделу
     *
     * @return string путь вида path.to.section. Для
     * корневого раздела возвращается пустая строка.
     */
    public function getActiveSectionPath()
    {
        return $this->sectionPath;
    }

    /**
     * Установить сокращенный путь к текущему обрабатываемому разделу
     *
     * @param string|[] $path путь вида path.to.section. Для
     * корневого раздела передается пустая строка.
     * Может быть передан массив ключей (пути) к разделу.
     * @param bool $sanitize очистить путь от промежуточных 
     * ключей "items". По умолчанию (true) очищать.
     * @return void
     */
    public function setActiveSectionPath($path, $sanitize=true)
    {
        if(is_array($path)) {
            $path=implode('.', $path);
        }

        if(!$path) {
            $path=self::ROOT;
        }

        if($sanitize) {
            $path=str_replace('.items.', '.', $path);
        }

        $this->sectionPath=$path;
    }

    /**
     * Получить общее кол-во обрабатываемых записей
     *
     * @param boolean $recalc пересчитать
     * @return int
     */
    public function getTotalCount($recalc=false)
    {
        if($recalc || ($this->totalCount === null)) {
            $this->totalCount=0;
            $sections=$this->getRootSections();
            foreach($sections as $section) {
                $this->totalCount+=$this->getSectionItemCount($section, true);
            }
        }

        return $this->totalCount;
    }

    /**
     * Установить общее кол-во обрабатываемых записей
     *
     * @param int|null $count общее кол-во обрабатываемых записей
     * @return void
     */
    public function setTotalCount($count)
    {
        if(is_numeric($count)) {
            $this->totalCount=(int)$count;
        }
        else {
            $this->totalCount=null;
        }
    }
    
    /**
     * Получить кол-во уже обработанных записей
     *
     * @return void
     */
    public function getProcessedCount()
    {
        return $this->processedCount;
    }

    /**
     * Установить кол-во уже обработанных записей
     *
     * @param int $count кол-во обработанных записей,
     * на которое нужно увеличить общее число обработанных 
     * записей.
     * @return void
     */
    public function setProcessedCount($count)
    {
        $this->processedCount=$count;

        if($this->processedCount > $this->getTotalProcessedCount()) {
            $this->setTotalProcessedCount($this->processedCount);
        }
    }

    /**
     * Увеличить кол-во уже обработанных записей
     *
     * @param int $count кол-во обработанных записей,
     * на которое нужно увеличить общее число обработанных 
     * записей. По умолчанию 1 (единица).
     * @return void
     */
    public function incProcessedCount($count=1)
    {
        $this->processedCount+=$count;
        $this->incTotalProcessedCount($count);
    }

    /**
     * Получить общее кол-во уже обработанных записей
     *
     * @return void
     */
    public function getTotalProcessedCount()
    {
        return $this->totalProcessedCount;
    }

    /**
     * Установить общее кол-во уже обработанных записей
     *
     * @param int $count кол-во обработанных записей,
     * на которое нужно увеличить общее число обработанных 
     * записей.
     * @return void
     */
    public function setTotalProcessedCount($count)
    {
        $this->totalProcessedCount=$count;
    }

    /**
     * Увеличить общее кол-во уже обработанных записей
     *
     * @param int $count кол-во обработанных записей,
     * на которое нужно увеличить общее число обработанных 
     * записей. По умолчанию 1 (единица).
     * @return void
     */
    public function incTotalProcessedCount($count=1)
    {
        $this->totalProcessedCount+=$count;
    }

    /**
     * Проверяет, возможно ли продолжение выполенения 
     * генерации карты сайта. 
     *
     * @return boolean
     */
    public function canNext()
    {
        return $this->getProcessedCount() < $this->getLimit();
    }

    /**
     * Получить полный путь (из ключей массива) к текущему 
     * обрабатываему разделу в массиве конфигурации.
     * @param bool $returnArray вернуть результат в виде массива.
     * По умолчанию (false) возвращать строку
     * @return string|[]
     */
    public function getActiveSectionFullPath($returnArray=false)
    {
        if($this->getActiveSectionPath() === self::ROOT) {
            $fullpath=self::ROOT;
        }
        else {
            $path=$this->getActiveSectionPath();
            if(strpos($path, self::ROOT . '.') !== 0) {
                $path=self::ROOT . '.items.' . $path;
            }
            $fullpath=trim(str_replace('.', '.items.', $path), '.');
        }

        return $returnArray ? explode('.', $fullpath) : $fullpath;
    }

    /**
     * Получение конфигурации корневых разделов
     *
     * @return []
     */
    public function getRootSections()
    {
        return A::rget($this->getConfig(), self::ROOT . '.items', []);
    }

    /**
     * Получить глубину вложенности текущего обрабатываемого
     * раздела, где 0 (нуль) это глубина корневого раздела.
     *
     * @return int
     */
    public function getActiveSectionDepth()
    {
        return substr_count($this->getActiveSectionPath(), '.');
    }

    /**
     * Получить суффикс для файла карты сайта 
     * текущего активного раздела.
     *
     * @return string
     */
    public function getActiveSectionSuffix()
    {
        $path=$this->getActiveSectionFullPath(true);
        $suffix='';
        while(($name=array_pop($path)) && ($name != self::ROOT)) {
            $prefix=A::rget($this->getConfig(), "{$path}.{$name}.prefix", '');
            $suffix=$prefix . ($prefix ? '-' : '') . $name . ($suffix ? '-' : '') . $suffix;
            $parent=array_pop($path);
            if($parent != 'items') {
                $this->addError('Структура конфигурации карты сайта задана неверно');
                return 'error';
            }
        }
        
        return $suffix;
    }

    /**
     * Установить активный раздел
     * @param string|[] сокращенный путь к текущему активному 
     * разделу вида "path.to.section". Можеть быть передан 
     * массив из частей пути
     * @param bool $sanitize очистить путь от промежуточных 
     * ключей "items". По умолчанию (true) очищать.
     * @return void
     */
    public function setActiveSection($paths, $sanitize=true)
    {
        $this->setOffset(0);
        $this->setActiveSectionPath($paths, $sanitize);
    }

    /**
     * Переход к следующему разделу.
     *
     * @param string|[]|null $path путь к разделу относительно которого 
     * будет установлен следующий активный раздел. Можеть быть передан 
     * массив из частей пути. По умолчанию (null) использовать путь 
     * к текущему активному разделу.
     * @return bool возвращает true, если раздел найден, или false, если 
     * следующий раздел не найден. 
     */
    public function nextActiveSection($path=null)
    {        
        if($path === null) $paths=$this->getActiveSectionFullPath(true);
        elseif(is_string($path)) $paths=explode('.', $path);
        elseif(is_array($path)) $paths=$path;
        else $paths=[];

        $currentSectionName=array_pop($paths);  
        if($currentSectionName === self::ROOT) {
            // корневой раздел является единственным
            $this->setActiveSection(self::ROOT);
            return false;
        }
        else {
            // ищем следующий подраздел
            $found=false;
            $nextSectionName=null;
            $siblings=A::rget($this->getConfig(), $paths, []);
            foreach(array_keys($siblings) as $name) {
                if($found) {
                    $nextSectionName=$name;
                    break;
                }
                $found=($name == $currentSectionName);
            }

            // если подраздел найден
            if($nextSectionName) {
                $paths[]=$nextSectionName;
                $this->setActiveSection($paths);
                return true;
            }
            // если подраздел не найден поднимаемся вверх
            else {
                // пропускаем системный ключ "items"
                $keyItems=array_pop($paths);
                if($keyItems != 'items') {
                    // конфигурация пути задана неверно
                    $this->addError('Структура конфигурации карты сайта задана неверно');
                    $this->setActiveSection('');
                    return false;
                }

                // и получаем следующий раздел в родительском разделе
                return $this->nextActiveSection($paths);
            }
        }

        $this->setActiveSection('');
        return false;
    }

    /**
     * Генерация карты сайта
     *
     * @return void
     */
    public function generate()
    {
        $this->resetErrors();
        $this->setProcessedCount(0);
        $this->generateActiveSection();
        $this->calcPercent();

        if($this->getPercent() === 100) {
            $this->saveIndex();
        }
    }

    /**
     * Сохранить индекс карты сайта
     *
     * @return void
     */
    public function saveIndex()
    {
        $sitemap=SitemapComponent::getInstance();
        $sitemap->setIndexPrefix(A::rget($this->getConfig(), self::ROOT . '.prefix', ''));
        $sitemap->saveIndex();
    }

    /**
     * Сгенерировать карту сайта для текущего раздела
     *
     * @return void
     */
    public function generateActiveSection()
    {
        $section=$this->getActiveSection();
        if(empty($section)) {
            $this->setPercent(-1);
        }
        else {
            $sitemap=new SitemapComponent;
            if(!HSitemap::getCallableValue(A::get($section, 'disabled', false))) {
                $sitemap->setIndexPrefix(A::rget($this->getConfig(), self::ROOT . '.prefix', ''));
                $sitemap->setPrefix(HSitemap::getCallableValue(A::get($section, 'prefix')));
                $sitemap->setSuffix(($this->getActiveSectionDepth() > 0) ? $this->getActiveSectionSuffix() : '');
                if(!$this->getActiveSectionDepth()) {
                    $sitemap->reset();
                }
                else {
                    $sectionDone=false;
                    if($className=A::get($section, 'class')) {
                        $criteria=HSitemap::getCallableValue(A::get($section, 'criteria', []), []);
                        $criteria=HDb::criteria($criteria);
                        $criteria->offset=$this->getOffset();
                        $criteria->limit=$this->getLimit();
                        if($models=$className::model()->findAll($criteria)) {
                            $modelsCount=count($models);                    
                            $this->incProcessedCount($modelsCount);                    
                            $this->setOffset(($modelsCount < $this->getLimit()) ? 0 : ($this->getOffset() + $modelsCount));
                            
                            foreach($models as $model) {
                                if($loc=HSitemap::getCallableValue(A::get($section, 'loc'), [$model])) {
                                    $lastMod=HSitemap::getAttributeValue($model, A::get($section, 'lastmod'));
                                    $priority=HSitemap::getPriority($model, A::get($section, 'priority'));
                                    $changeFreq=HSitemap::getChangeFreq($model, A::get($section, 'changefreq'));
                                    $sitemap->add($loc, $lastMod, $priority, $changeFreq);
                                }
                            }

                            if($modelsCount < $this->getLimit()) {
                                $sectionDone=true;
                            }
                            else {
                                $criteria->offset++;
                                $sectionDone=!$className::model()->count($criteria);
                            }
                        }
                        else {
                            $sectionDone=true;
                        }
                    }
                    elseif($loc=HSitemap::getCallableValue(A::get($section, 'loc'), [null])) {
                        $this->incProcessedCount();
                        $lastMod=HSitemap::getCallableValue(A::get($section, 'lastmod'), [null]);
                        $priority=HSitemap::getPriority(null, A::get($section, 'priority'));
                        $changeFreq=HSitemap::getChangeFreq(null, A::get($section, 'changefreq'));
                        $sitemap->add($loc, $lastMod, $priority, $changeFreq);
                        $sectionDone=true;
                    }

                    $sitemap->save($sectionDone);
                }                

                if($this->canNext()) {
                    // если у раздела есть подразделы, то переходим к их обработке
                    foreach(array_keys(A::get($section, 'items', [])) as $name) {
                        $this->setOffset(0);
                        $this->setActiveSectionPath($this->getActiveSectionPath() . '.' . $name);
                        $this->generateActiveSection();
                        if(!$this->canNext()) break;
                    }
                }

                if($this->canNext() && $this->nextActiveSection()) {
                    $this->generateActiveSection();
                }
            }
            else {
                if($this->canNext() && $this->nextActiveSection()) {
                    $this->generateActiveSection();
                }
            }
        }
    }
}