<?php
namespace seo\ext\sitemap\components;

use common\components\helpers\HYii as Y;
use common\components\helpers\HFile;
use seo\ext\sitemap\components\helpers\HSitemap;

class Sitemap
{
    /**
     * @var int максимальное кол-во записей
     */
    const ITEM_PER_SITEMAP = 50000;

    /**
     * URL XSLT схемы отображения по умолчанию
     */
    const XSLT_DEFAULT_URL='/data/sitemap-xsl/sitemap.xsl';
    const XSLT_DEFAULT_PATH='seo.ext.sitemap.data.sitemap-xsl';
    const XSLT_DEFAULT_FILE='sitemap.xsl';

    const SCHEMA='http://www.sitemaps.org/schemas/sitemap/0.9';
    const DEFAULT_PRIORITY=1;
    const DEFAULT_CHANGEFREQ='weekly';
	
    private $path='webroot.sitemaps';
    private $tmp='application.runtime.sitemaps';    
    private $separator='-';
    private $indexPrefix='';
    private $prefix='';
    private $suffix='';
    private $xslt=null;
    private $items=[];

    private static $instance=null;

    /**
     * Получить статический экземпляр класса
     *
     * @return Sitemap
     */
    public static function getInstance()
    {
        if(static::$instance === null) {
            static::$instance=new static;
        }

        return static::$instance;
    }

    /**
     * Инициализация создания полной карты сайта
     *
     * @return void
     */
    public function reset()
    {
        $this->resetItems();

        // Удаление всех временных файлов
        HFile::rm(\Yii::getPathOfAlias($this->tmp), true, true);

        // Удаление всех файлов XML
        HFile::readDir(\Yii::getPathOfAlias($this->path), true, function($dirname, $entry, $params) {
            if(is_file($entry) && (pathinfo($entry, PATHINFO_EXTENSION) == 'xml') && (strpos(basename($entry), 'sitemap') === 0)) {
                @unlink($entry);
            }
        });
    }

    /**
     * Сбросить записи
     *
     * @return void
     */
    public function resetItems()
    {
        $this->items=[];
    }

    /**
     * Добавить запись в карту сайта
     *
     * @param string $loc URL страницы, будет приведен к 
     * абсолютному URL относительно текущего домена
     * @param \DateTime|string|int|null $lastmod последняя дата и время модификации страницы
     * @param integer $priority приоритет. По умолчанию 0.5.
     * @param string|null $changeFreq частота обновления
     * @return bool
     */
    public function add($loc, $lastmod=null, $priority=0.5, $changefreq=null)
    {
        $this->items[]=[$loc, $lastmod, $priority, $changefreq];
    }

    /**
     * Получить базовое имя файла карты сайта 
     * (без пути и расширения)
     *
     * @return string
     */
    public function getBasename()
    {
        $filename='sitemap';
        
        if($this->getPrefix()) {
            $filename.=$this->separator . $this->getPrefix();
        }
        
        $filename.=$this->getSuffix();

        return $filename;
    }

    /**
     * Получить базовое имя файла индекса карты сайта 
     * (без пути и расширения)
     *
     * @return string
     */
    public function getIndexBasename()
    {
        $filename='sitemap';
        
        if($this->getIndexPrefix()) {
            $filename.=$this->separator . $this->getIndexPrefix();
        }
        
        return $filename;
    }

    /**
     * Получить имя XML файла индекса карты сайта
     *
     * @return string
     */
    public function getIndexFileName()
    {
        return HFile::path([\Yii::getPathOfAlias($this->path), $this->getIndexBasename() . '.xml'], true, 0755);
    }

    /**
     * Получить имя XML файла карты сайта
     *
     * @param int $part номер части XML файла. 
     * По умолчанию 0 (нуль).
     * 
     * @return string
     */
    public function getXmlFileName($part=0)
    {
        return HFile::path([
            \Yii::getPathOfAlias($this->path), 
            $this->getBasename() . ($part ? ($this->separator . (int)$part) : '') . '.xml'
        ], true, 0755);
    }

    /**
     * Получить имя временного файла карты сайта
     *
     * @return string
     */
    public function getTmpFileName()
    {
        return HFile::path([\Yii::getPathOfAlias($this->tmp), $this->getBasename() . '.csv'], true, 0755);
    }

     /**
     * Получить имя временного файла индекса карты сайта
     *
     * @return string
     */
    public function getIndexTmpFileName()
    {
        return HFile::path([\Yii::getPathOfAlias($this->tmp), $this->getIndexBasename() . '.csv'], true, 0755);
    }

    /**
     * Получить относительную ссылку на файл карты сайта
     *
     * @param int $part номер части XML файла. 
     * По умолчанию 0 (нуль).
     * @return string
     */
    public function getUrl($part=0)
    {
        return HFile::pathToUrl($this->getXmlFileName($part));
    }

    /**
     * Получить абсолютную ссылку на файл карты сайта
     * 
     * @param int $part номер части XML файла. 
     * По умолчанию 0 (нуль).
     * @return string
     */
    public function getAbsoluteUrl($part=0)
    {
        return Y::createAbsoluteUrl($this->getUrl($part));
    }

    /**
     * Получить URL XLST шаблона для отображения карты сайта
     *
     * @return string|null
     */
    public function getXslt()
    {
        return $this->xslt;
    }

    /**
     * Установить URL XLST шаблона для отображения карты сайта
     *
     * @param string $xslt
     * @return void
     */
    public function setXslt($url)
    {
        $this->xslt=$url;
    }

    /**
     * Получить URL XSLT схемы отображения для карты сайта
     *
     * @return void
     */
    public function getXsltUrl()
    {
        if($this->getXslt() === null) {
            $this->setXslt(self::XSLT_DEFAULT_URL);
            $xsltFile=HFile::normalizePath(HFile::path([\Yii::getPathOfAlias('webroot'), $this->getXslt()], true, 0755));
            if(!is_file($xsltFile)) {
                $sourceXsltFile=HFile::normalizePath(\Yii::getPathOfAlias(self::XSLT_DEFAULT_PATH) . '/' . self::XSLT_DEFAULT_FILE);
                copy($sourceXsltFile, $xsltFile);
            }
        }

        return $this->getXslt() ?: null;
    }

    /**
     * Установить префикс для файлов карты сайта
     *
     * @param string $prefix префикс
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix=$prefix;
    }

    /**
     * Получить префикс для файлов карты сайта
     *
     * @return string
     */
    public function getPrefix()
    {
        return is_string($this->prefix) ? $this->prefix : '';
    }

    /**
     * Установить префикс для файла индекса карты сайта
     *
     * @param string $prefix префикс
     * @return void
     */
    public function setIndexPrefix($prefix)
    {
        $this->indexPrefix=$prefix;
    }

    /**
     * Получить префикс для файла индекса карты сайта
     *
     * @return string
     */
    public function getIndexPrefix()
    {
        return is_string($this->indexPrefix) ? $this->indexPrefix : '';
    }

    /**
     * Установить suffix для файлов карты сайта
     *
     * @param string|[] $suffix дополнение к имени файла.
     * Может быть передан массив из частей.
     * @return void
     */
    public function setSuffix($suffix)
    {
        $this->prefix=$suffix;
    }

    /**
     * Получить suffix для файлов карты сайта
     *
     * @return string
     */
    public function getSuffix()
    {
        if(!empty($this->suffix)) {
            return $this->separator . (is_array($this->suffix) ? implode($this->separator, $this->suffix) : $this->suffix);
        }

        return ''; 
    }

    /**
     * Сохранение карты сайта
     * @param boolean $done процесс завершен, данные записываются
     * в файл XML, иначе записываются по временный файл CSV. 
     * По умолчанию (true) процесс завершен.
     * @return void
     */
    public function save($done=true)
    {
        $this->saveAsCSV();
        if($done) {
            $this->saveAsXML();
        }
    }

    /**
     * Сохранение индекса карты сайта
     * Необходимо запускать после формирования всех файлов карты сайта.
     * После сохранения удаляет временный файл.
     * @return bool
     */
    public function saveIndex()
    {
        $writer=$this->getXMLWriter($this->getIndexFileName());
        $writer->startElement('sitemapindex');
        $writer->writeAttribute('xmlns', self::SCHEMA);

        $locs=[];
        /** @var [] $row [loc] */
        foreach($this->readCsv($this->getIndexTmpFileName()) as $row) {
            if(!empty($row[0]) && !isset($locs[$row[0]])) {
                $writer->startElement('sitemap');
                $writer->writeElement('loc', $row[0]);
                $writer->writeElement('lastmod', HSitemap::normalizeDate(time()));
                $writer->endElement();
                $locs[$row[0]]=1;
            }
        }

        $writer->endElement();
        $writer->endDocument();

        @unlink($this->getIndexTmpFileName());
    }

    /**
     * Запись XML файла
     * TODO: сделать разбиение по Sitemap::ITEM_PER_SITEMAP
     * После сохранения удаляет временный файл
     * @return void
     */
    protected function saveAsXML()
    {
        $fOpenWriter=function($part=0) {
            $writer=$this->getXMLWriter($this->getXmlFileName($part));
            $writer->startElement('urlset');
            $writer->writeAttribute('xmlns', self::SCHEMA);
            $writer->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $writer->writeAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
            if($part > 0) $this->appendToCSV($this->getIndexTmpFileName(), [[$this->getAbsoluteUrl($part)]]);
            return $writer;
        };
        $fCloseWriter=function($writer) {
            $writer->endElement();
            $writer->endDocument();
        };

        $nrow=0;
        $locs=[];
        $writer=$fOpenWriter();
        /** @var [] $row [loc, lastmod, priority, changefreq] */
        foreach($this->readCsv($this->getTmpFileName()) as $row) {
            if(empty($row[0]) || isset($locs[$row[0]])) continue;
            $writer->startElement('url');
            $writer->writeElement('loc', Y::createAbsoluteUrl($row[0]));
            $writer->writeElement('lastmod', HSitemap::normalizeDate($row[1]));
            $writer->writeElement('priority', $row[2] ?: (\D::cms('sitemap_priority')?:self::DEFAULT_PRIORITY));
            $writer->writeElement('changefreq', $row[3] ?: (\D::cms('sitemap_changefreq')?:self::DEFAULT_CHANGEFREQ));
            $writer->endElement();
            
            $nrow++;
            if(!($nrow % self::ITEM_PER_SITEMAP)) {
                $fCloseWriter($writer);
                $writer=$fOpenWriter(ceil($nrow / self::ITEM_PER_SITEMAP) + 1);
            }

            $locs[$row[0]]=1;
        }

        $fCloseWriter($writer);

        @unlink($this->getTmpFileName());
    }

    /**
     * Запись временного файла CSV
     *
     * @param [type] $filename
     * @return void
     */
    protected function saveAsCSV()
    {
        if(!empty($this->items)) {
            $this->appendToCSV($this->getTmpFileName(), $this->items);
            $this->appendToCSV($this->getIndexTmpFileName(), [[$this->getAbsoluteUrl()]]);
        }
    }

    /**
     * Добавить данные в CSV файл
     *
     * @param string $filename имя файла
     * @param [] $data данные
     * @return void
     */
    protected function appendToCSV($filename, $data)
    {
        HFile::csv($filename, $data, ';', '"', '\\', false, 'a+');
    }

    /**
     * Прочитать CSV файл
     *
     * @param string $filename имя файла
     * @return itarable возвращает итератор
     */
    protected function readCSV($filename)
    {
        return HFile::readCsvIterator($filename, ';', '"', '\\');
    }

    /**
     * Получить XMLWriter
     *
     * @param string $filename имя XML файла
     * @return \XMLWriter
     */
    protected function getXMLWriter($filename)
    {
        $writer = new \XMLWriter();
        $writer->openURI($filename);
        $writer->startDocument('1.0', 'UTF-8');
        $writer->setIndent(true);
            
        if($this->getXsltUrl()) {
            $writer->writePi('xml-stylesheet', 'type="text/xsl" href="' . $this->getXsltUrl() . '"'); 
        }

        return $writer;
    }
}