<?php
namespace Kontur\CheckPrice;

use Bitrix\Main\Data\Cache;

class PriceTagCollection
{
    private $collection=[];
    private static $instance=null;

    public static function getInstance()
    {
        if(static::$instance === null) {
            static::$instance = new static;            
        }

        return static::$instance;
    }

    public function __construct()
    {
        $this->loadCollection();
    }

    public function add($id)
    {
        $this->collection[(int)$id]=[
            'ID'=>(int)$id
        ];
        
        $this->saveCollection();

        return $this;
    }

    public function remove($id)
    {
        if(isset($this->collection[(int)$id])) {
            unset($this->collection[(int)$id]);
        }
        
        $this->saveCollection();

        return $this;
    }

    public function exists($id)
    {
        return isset($this->collection[(int)$id]);
    }

    public function count()
    {
        return count($this->collection);
    }

    public function getCollectionData()
    {
        return $this->collection;
    }

    protected function cache($write=false)
    {
        $cacheEngine=Cache::createCacheEngine();
        $cache=new class($cacheEngine) extends Cache {
            public function getCacheId() { global $USER; return $USER->GetId(); }
            public function set($data) {
                static::$clearCacheSession=false;static::$clearCache=false;
                $this->forceRewriting=true;
                $this->initCache(86400, $this->getCacheId(), '/kpricetag');
                if($this->startDataCache()) { $this->endDataCache($data); }
                $this->forceRewriting=false;
            }
            public function get() {
                static::$clearCacheSession=false;static::$clearCache=false;
                $data=$this->initCache(86400, $this->getCacheId(), '/kpricetag') ? $this->getVars() : null;
                return is_array($data) ? $data : [];
            }
        };

        return $write ? (new $cache($cacheEngine))->set($this->collection) : (new $cache($cacheEngine))->get();
    }

    protected function saveCollection()
    {
        $this->cache(true);
    }

    protected function loadCollection()
    {
       $this->collection=$this->cache();
    }
}