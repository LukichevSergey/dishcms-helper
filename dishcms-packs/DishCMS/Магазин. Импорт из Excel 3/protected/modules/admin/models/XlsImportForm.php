<?php
/**
 * ПРОВЕРЬТЕ ПРАВА НА ЗАПИСЬ В ПАПКУ application.runtime.xlsimport
 */
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HFile;
use common\components\helpers\HTools;
use common\components\helpers\HHash;

// PHPExcel
\Yii::import('application.vendors.PHPExcel.PHPExcel');
include_once(\Yii::getPathOfAlias('application.vendors.PHPExcel.PHPExcel').'/IOFactory.php');

class XlsImportForm extends \common\components\base\FormModel
{
	/**
	 * @var integer pseudo id.
	 */
	public $id=1;
	
	public $filename;
	public $sheet=1;
	public $header_row=2;
	public $eav_first_column=8;
	public $limit=250;
	public $delay=1;
	
	public $clear_products=false;
	public $clear_categories=false;
	public $clear_eav_attributes=false;
	
	public $images_path='/uploads/xlsimport';
	
	public $hash;
	
	private $ext;
	private $iteration=0;
	private $eavLinks=[];
	private $categoryLinks=[];
	private $cacheId;
	private $highestRow;
	private $cacheAttributes=['sheet', 'limit', 'header_row', 'eav_first_column', 'delay', 'images_path'];
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['filename', 'file', 'allowEmpty'=>false, 'except'=>'next'],
			['sheet, limit, header_row, eav_first_column, delay', 'numerical', 'integerOnly'=>true],
			['images_path, hash', 'safe'],
			['clear_products, clear_categories, clear_eav_attributes', 'boolean']
		]);
	}

	/**
	 * (non-PHPdoc)
	 * @see \CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
			'filename'=>'Файл импорта (*.xls)',		    
			'sheet'=>'Номер листа, на котором находится список товаров',
		    'header_row'=>'Номер строки заголовков',
		    'eav_first_column'=>'Номер колонки, с которой начинаются дополнительные атрибуты',
		    'limit'=>'Кол-во товаров выгружаемых за один шаг',	
		    'delay'=>'Задержка в секундах между итерациями импорта',
		    'images_path'=>'Путь к картинкам для товаров, относительно корня сайта',		    
			'clear_products'=>'Перед импортом удалить все товары',
			'clear_categories'=>'Перед импортом удалить все категории',
		    'clear_eav_attributes'=>'Перед импортом удалить все дополнительные атрибуты товара'		    
		]);
	}
	
	public function getIteration()
	{
	    return (int)$this->iteration;
	}
	
	public function setIteration($iteration)
	{
	    $this->iteration=$iteration;
	}
	
	public function getPercent()
	{
	    $iterations=ceil($this->getTotalRows() / $this->limit);
	    $percent=round((100 / $iterations) * $this->iteration, 2, PHP_ROUND_HALF_UP);
	    
	    return ($percent > 100) ? 100 : $percent;
	}
	
	public function getDelay()
	{
	    return (int)$this->delay;
	}
	
	public function getLimit()
	{
	    return (int)$this->limit;
	}
	
	public function getTotalRows()
	{
	    return ($this->highestRow - $this->header_row);
	}
	
	public function getHeaderRow()
	{
	    return (int)$this->header_row;
	}
	
	public function getStartRow()
	{
	    if($this->iteration === 0) {
	        return $this->getHeaderRow();
	    }
	    
	    return ($this->getHeaderRow() + 1) + (($this->getIteration() - 1) * $this->getLimit());
	}
	
	public function getEndRow()
	{
	    if($this->iteration === 0) {
	        return $this->getHeaderRow();
	    }
	    
	    return $this->getStartRow() + $this->getLimit();
	}
	
	public function getSheet()
	{
	    return (int)$this->sheet;
	}
	
	public function getImportedCount()
	{
	    $count=($this->getIteration() * $this->getLimit());
	    
	    if($count > $this->getTotalRows()) {
	        $count=$this->getTotalRows();
	    }
	    
	    return $count;
	}
	
	public function getAjaxData()
	{
	    return [
	        'hash'=>$this->hash,
	        'iteration'=>$this->getIteration(),
	        'percent'=>$this->getPercent(),
	        'imported_count'=>$this->getImportedCount()
	    ];
	}
	
	public function setCache($key, $data)
	{
	    $content=json_encode($data);
	    file_put_contents($this->getCacheFile($this->getCacheId($key)), $content);
	}
	
	public function getCache($key)
	{
	    $filename=$this->getCacheFile($this->getCacheId($key));
	    
	    if(is_file($filename)) {
	        return json_decode(file_get_contents($filename), true);
	    }
	    
	    return null; 
	}
	
	public function setCacheAttributes()
	{
	    $attributes=$this->getAttributes($this->cacheAttributes);
	    $attributes['ext']=$this->ext;
	    $attributes['highestRow']=$this->highestRow;
	    
	    $this->setCache('attributes', $attributes);	        
	}
	
	public function loadCacheAttributes()
	{
	    $attributes=$this->getCache('attributes');	    
	    if(!empty($attributes)) {
	        foreach($attributes as $name=>$value) {
	            $this->$name=$value;
	        }
	    }
	}
	
	public function clearCache()
	{
	    // удаление файлов кэша импорта
	    array_map('unlink', HFile::getFiles(\Yii::getPathOfAlias('application.runtime.xlsimport'), true));
	}
	
	public function import()
	{
		if($this->validate()) {
		    if($this->getIteration() === 0) {
		        $this->clearCache();
		        $this->clearCatalog();
		    }
		    
			$data=$this->prepareData();
			
			if($this->getIteration() === 0) {
			    $this->setCacheAttributes();
			}
			elseif($this->getIteration() > 0) {
			    $this->loadCacheAttributes();
			    
    			$codes=array_map(
    			    function($code) { return HDb::qv($this->normalizeString($code)); },
    			    array_filter(array_column($data, 0), function($code) { return !empty($code); })
    			);
    			
    			if(!empty($codes)) {
                    $exists=[];
        			$products=HDb::queryAll('SELECT `id`, `code` FROM `product` WHERE `code` IN (' . implode(',', $codes) . ')');
        			foreach($products as $product) {
        			    $code=$this->normalizeString($product['code']);
        			    $exists[$code]=(int)$product['id'];
        			}
        			unset($products);
    			
        			$product=new \Product;
        			$hasEav=(count($this->eavLinks) > 0);
        			$imagesPath=$product->mainImageBehavior->getPath() . '/';
        			$imagesUploadPath=\Yii::getPathOfAlias('webroot') . preg_replace('#/+#', '/', "/{$this->images_path}/");
        			$images=[];
        			$newests=[];
        			$updates=[];
        			$eavs=[];
        			foreach($data as $row=>$rowData) {
        				// 0 - код товара
        				// 1 - наименование
        				// 2 - раздел
        				// 3 - доллар
        				// 4 - евро
        				// 5 - рубль
        				// 6 - фото
        				// остальное атрибуты товара
        			    if($code=$this->normalizeString($rowData[0])) {
        			        $product->isNewRecord=empty($exists[$code]);
        			        
        			        $title=$this->normalizeString($rowData[1]);
        			        $fields=[
        			            $code,
        			            $title,
        			            $this->getAlias($product, $title),
        			            $this->getCategoryId($rowData[2]),
        			            $this->normalizePrice($rowData[3]),
        			            $this->normalizePrice($rowData[4]),
        			            $this->normalizePrice($rowData[5])
        			        ];
        			        
        			        if($product->isNewRecord) {
        			            $newests[]=$fields;
        			        }
        			        else {
        			            $updates[$exists[$code]]=$fields;
        			        }
        			        
        			        $imageFile=trim($rowData[6]);
        			        if(!empty($imageFile) && is_file($imagesUploadPath . $imageFile)) {
        			            $images[$code]=$imagesUploadPath . $imageFile;
        			        }
        			        
        			        if($hasEav) {
        			            foreach($this->eavLinks as $eavColumnIdx=>$eavAttributeId) {
        			                $eavs[$code][$eavAttributeId]=A::get($rowData, $eavColumnIdx, '');
        			            }
        			        }
        			    }
        			}
        			
        			if(!empty($newests)) {
        			    $this->queryInsert('product', 'code, title, alias, category_id, price_usd, price_eur, price', $newests);
        			}
        			
        			if(!empty($updates)) {
        			    $this->queryUpdate('product', 'code, title, alias, category_id, price_usd, price_eur, price', $updates);
        			}
        			
        			$imageUpdates=[];
        			$eavReplaces=[];
    			
        			$products=HDb::queryAll('SELECT `id`, `code`, `main_image` FROM `product` WHERE `code` IN (' . implode(',', $codes) . ')');
        			foreach($products as $product) {
        			    $id=(int)$product['id'];
        			    $code=$this->normalizeString($product['code']);
        			    
        			    // обработка изображений товара
        			    if(!empty($images[$code])) {
        			        if(!empty($product['main_image']) && is_file($imagesPath . $product['main_image'])) {
        			            @unlink($imagesPath . $product['main_image']);
            			    }
            			    $imageFile=HHash::u($id.'_') . '.' . pathinfo($images[$code], PATHINFO_EXTENSION);
            			    copy($images[$code], $imagesPath . $imageFile);
            			    $imageUpdates[(int)$product['id']]=[$imageFile];			        
        			    }
        			    
        			    // обработка дополнительных атрибутов товара
        			    // @todo у таблицы eav_value должен быть создан уникальный индекс (id_attrs, id_product)
        			    if(!empty($eavs[$code])) {
        			        foreach($eavs[$code] as $eavAttributeId=>$eavValue) {
        			            $eavReplaces[]=[$eavAttributeId, $id, (string)$eavValue];
        			        }
        			    }
        			}
        			unset($products);
    			
    			
        			if(!empty($imageUpdates)) {
        			    $this->queryUpdate('product', 'main_image', $imageUpdates);
        			}
        			
        			if(!empty($eavReplaces)) {
        			    $this->queryInsert('eav_value', 'id_attrs, id_product, value', $eavReplaces, true);
        			}
    			}
			}
			
			return $this->getPercent();
		}

		return false;
	}

	protected function getAlias($model, $title, $attribute='alias')
	{
	    $alias=HTools::alias($title);
		
		$criteria=[];
		if(!$model->isNewRecord) {
		    $criteria=['condition'=>'`id`<>:id', 'params'=>['id'=>$model->id]];
		}
		
		if($model->wcolumns([$attribute=>$alias])->exists($criteria)) {
			return $alias . HHash::u('-');
		}
		
		return $alias;
	}
	
	protected function getCategoryId($path)
	{
	    if(!empty($path)) {
	        $hash=md5($path);
	        
	        if(isset($this->categoryLinks[$hash])) {
	            return $this->categoryLinks[$hash];
	        }
	        
	        $paths=explode(';', $path);
			$title=array_shift($paths);
			
			if(preg_match('/Каталог/i', $title)) {
				if(empty($paths)) return;
				$title=array_shift($paths);
			}
			
			if(!($root=\Category::model()->roots()->wcolumns(['title'=>$title])->find())) {
			    $root=new \Category;
			    $root->title=$title;
			    $root->alias=$this->getAlias($root, $title);
			    $root->saveNode();
			}
			
			if(empty($paths)) {
			    $this->categoryLinks[$hash]=$root->id;
			    return $root->id;
			}
			
			$parent=$root;
			while($title=array_shift($paths)) {
				if(!trim($title)) continue;
				if(!($category=$parent->children()->wcolumns(['title'=>$title])->find())) {
					$category=new \Category;
					$category->title=$title;
					$category->alias=$this->getAlias($category, $title);
					$category->appendTo($parent);
				}				
				$parent=$category;
			}
			
			$this->categoryLinks[$hash]=$parent->id;
			return $parent->id;
		}
		
		return null;
	}

	protected function getCacheFile($ext=null)
	{
	    if(!$ext) {
	        $ext=$this->ext;
	    }
	    
		return \Yii::getPathOfAlias('application.runtime.xlsimport') . '/' . $this->hash . '.' . $ext;
	}
	
	protected function getCacheCsvFile()
	{
	    return $this->getCacheFile('csv');
	}
	
	protected function getCacheId($key='')
	{
	    if(!$this->cacheId) {
	        $this->cacheId=md5($this->getCacheCsvFile());
	    }
	    
        return $this->cacheId . $key;
	}
	
	protected function getCsvData()
	{
	    $data=[];
	    
	    if (($handle=fopen($this->getCacheCsvFile(), "r")) !== false) {
	        $i=0;
	        $start=$this->getStartRow() - 1;
	        $end=$this->getEndRow() - 1;
	        if($start === $end) $end += 1;
	        while (($row=fgetcsv($handle, 0, ";", '"')) !== FALSE) {
	            if(($i >= $start) && ($i < $end)) {
	               $data[] = $row;
	            }
	            elseif($i > $end) {
	                break;
	            }
	            $i++;
	        }
	        fclose($handle);
	    }
	    
	    return $data;
	}

	protected function prepareData()
	{
		$data=[];

		if($this->getIteration() === 0) {
    		$file=\CUploadedFile::getInstance($this, 'filename');
    		if($file instanceof \CUploadedFile) {
    		    $this->hash=md5(HHash::u() . md5($file->getName()));
    		    $this->ext=$file->getExtensionName();
    			$file->saveAs($this->getCacheFile());
    		}
		}

		try {
		    if(!is_file($this->getCacheCsvFile()) && is_file($this->getCacheFile())) {
		        $fileType=\PHPExcel_IOFactory::identify($this->getCacheFile());
			    $reader=\PHPExcel_IOFactory::createReader($fileType);
			    $excel=$reader->load($this->getCacheFile());
			    $sheet=$excel->getSheet($this->getSheet() - 1);
			    $this->highestRow=$sheet->getHighestRow(); 
			    
			    $writer=\PHPExcel_IOFactory::createWriter($excel, 'CSV');
			    $writer->setDelimiter(';');
			    $writer->setEnclosure('"');
			    $writer->setSheetIndex($this->getSheet() - 1);
			    $writer->save($this->getCacheCsvFile());
			    
			    @unlink($this->getCacheFile());
		    }
		    
		    if(!is_file($this->getCacheCsvFile())) {
		        throw new \CException('Не удалось прочитать файл импорта');
		    }
		    
		    $data=$this->getCsvData();
		    
			// подготовка выгрузки дополнительных атрибутов товара
			if($this->getIteration() === 0) {
			    $headerRowData=$data[0];
				$headerColumnCount=count($headerRowData);
				if($headerColumnCount >= $this->eav_first_column) {
				    $eavAttributes=[];
				    
				    $names=HDb::queryColumn('SELECT `name` FROM `eav_attribute`');
				    foreach($names as $name) {
				        $key=$this->normalizeString($name, true);
				        $eavAttributes[$key]=true;
			        }				    
				    
				    $newests=[];
				    for($i=($this->eav_first_column - 1); $i<$headerColumnCount; $i++) {
				        $key=$this->normalizeString($headerRowData[$i], true);
				        if(!empty($key)) {
				            if(!isset($eavAttributes[$key])) {
				                $newests[]=[$this->normalizeString($headerRowData[$i]), 1];
				            }
				            $eavAttributes[$key]=$i;
				        }
				    }
				    $this->queryInsert('eav_attribute', 'name, fixed', $newests);
				        
				    $this->eavLinks=[];
				    $eavs=HDb::queryAll('SELECT `id`, `name` FROM `eav_attribute`');
			        foreach($eavs as $eav) {
			            $key=$this->normalizeString($eav['name'], true);
			            if(!empty($eavAttributes[$key]) && ($eavAttributes[$key] !== true)) {
			                $this->eavLinks[$eavAttributes[$key]]=$eav['id'];
			            }
			        }
				}
				$this->setCache('eav', $this->eavLinks);
			}
			else {
			    $this->eavLinks=$this->getCache('eav');
			}		
		} 
		catch(Exception $e) {
		    $this->addError('filename', 'Ошибка при чтении файла "' . basename($file->getName()) . '": ' . $e->getMessage());
		}

		return $data;
	}

	/**
	 * Полная очистка каталога от категорий и товаров.
	 * Дополнительно удаляются все файлы и изображения.
	 * @return boolean
	 */
	protected function clearCatalog()
	{
	    $queries=[];
	
	    $paths=[];
	    
	    if($this->clear_products) {
	        $paths[]='/images/product';
	        $paths[]='/files/product';
	        
	        $queries[]='DELETE FROM `product`';
	        $queries[]='DELETE FROM `related_category`';
	        $queries[]='DELETE FROM `image` WHERE `model`=\'product\'';
	        $queries[]='DELETE FROM `file` WHERE `model`=\'product\'';
	        $queries[]='DELETE FROM `metadata` WHERE `owner_name`=\'product\'';
	        $queries[]='ALTER TABLE `product` AUTO_INCREMENT=1';
	    }
	        
        if($this->clear_categories) {
            $paths[]='/images/category';
            $paths[]='/files/category';
            
            $queries[]='DELETE FROM `category`';
            $queries[]='DELETE FROM `image` WHERE `model`=\'category\'';
            $queries[]='DELETE FROM `file` WHERE `model`=\'category\'';
            $queries[]='DELETE FROM `metadata` WHERE `owner_name`=\'category\'';
            $queries[]='ALTER TABLE `category` AUTO_INCREMENT=1';
        }
	        
        if($this->clear_products || $this->clear_categories) {
    	    $queries[]='SET @n:=0; UPDATE `image` SET `id`=@n:=@n+1; ALTER TABLE `image` AUTO_INCREMENT=1';
    	    $queries[]='SET @n:=0; UPDATE `file` SET `id`=@n:=@n+1; ALTER TABLE `file` AUTO_INCREMENT=1';
    	    $queries[]='SET @n:=0; UPDATE `metadata` SET `id`=@n:=@n+1; ALTER TABLE `metadata` AUTO_INCREMENT=1';
    	    
    		// удаление картинок и файлов
    	    $webroot=\Yii::getPathOfAlias('webroot');
		    foreach($paths as $path) {
		        if(is_dir("{$webroot}{$path}")) {
	                array_map('unlink', HFile::getFiles("{$webroot}{$path}", true));
				}
			}
	    }
	    
	    if($this->clear_eav_attributes) {
	        $queries[]='DELETE FROM `eav_value`';
	        $queries[]='DELETE FROM `eav_attribute`';
	        $queries[]='ALTER TABLE `eav_value` AUTO_INCREMENT=1';
	        $queries[]='ALTER TABLE `eav_attribute` AUTO_INCREMENT=1';
	    }
	        
	    $this->query($queries);  

		return true;

	}
	
	protected function query($queries=[])
	{
	    if(!empty($queries)) {
            HDb::execute(implode(';', A::toa($queries)));
	    }
	}
	
	protected function queryInsert($tableName, $fields=[], $values=[], $replace=false)
	{
	    if(!empty($fields) && !empty($values)) {
	        $query=($replace ? 'REPLACE ' : 'INSERT INTO ') . HDb::qt($tableName);
	        
	        if(is_array($fields)) $query.=' (' . implode(',', $fields) . ')';
	        else $query.=" ({$fields})";
	        
	        $query.=' VALUES ';
	        if(is_array($values)) {
	           $_values=[];
	           foreach($values as $value) {
	               if(is_array($value)) {
	                   $_value=[];
	                   foreach($value as $v) {
	                       if(is_string($v)) $_value[]=HDb::qv($v);
	                       else $_value[]=$v;
	                   }
	                   $_values[]=implode(',', $_value);
	               }
	               else {
	                   $_values[]=$value;
	               }
	           }
	           $query.='(' . implode('),(', $_values) . ')';
	        }
	        else {
	            $query.="({$values})";
	        }
	        
	        HDb::execute($query);
	    }
	}
	
	protected function queryUpdate($tableName, $fields=[], $values=[])
	{
	    if(!empty($fields) && !empty($values)) {
	        if(is_string($fields)) {
	            $fields=explode(',', preg_replace('/\s+/', '', $fields));
	        }
	        
	        $queries=[];
	        $queryHead='UPDATE ' . HDb::qt($tableName) . ' SET ';
	        foreach($values as $id=>$value) {
	            $query=$queryHead;
	            if(is_string($value)) {
	                $query .= $value;
	            }
	            elseif(is_array($value)) {
	                $comma='';
    	            foreach($fields as $idx=>$field) {
    	                $v=$value[$idx];
    	                if(is_string($v)) $v=HDb::qv($v);
    	                $query.="{$comma}{$field}={$v}";
    	                $comma=',';
    	            }
	            }
	            $query.=" WHERE `id`={$id}";
	            $queries[]=$query;
	        }
	        
	        if(!empty($queries)) {
	            HDb::execute(implode(';', $queries));
	        }
	    }
	}
	
	protected function normalizeString($str, $lower=false)
	{
	    $str=trim(preg_replace('/\s+/', ' ', $str));
	    
	    if($lower) {
	        return mb_strtolower($str);
	    }
	    
	    return $str;
	}
	
	protected function normalizePrice($price)
	{
	    return (float)str_replace(',', '.', $price);
	}
}
