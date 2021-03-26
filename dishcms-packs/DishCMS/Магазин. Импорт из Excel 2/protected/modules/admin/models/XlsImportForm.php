<?php
/**
 * ПРОВЕРЬТЕ ПРАВА НА ЗАПИСЬ В ПАПКУ application.runtime.xlsimport
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HFile;
use common\components\helpers\HTools;
use common\components\helpers\HHash;
use common\components\helpers\HCurrency;

\Yii::import('application.vendors.PHPExcel.PHPExcel');
include_once(\Yii::getPathOfAlias('application.vendors.PHPExcel.PHPExcel').'/IOFactory.php');

class XlsImportForm extends \common\components\base\FormModel
{
	/**
	 * @var integer pseudo id.
	 */
	public $id=1;
	
	/**
	 * @var string|false файл импорта настроек.
	 */
	public $import_filename=false;

	public $sheet_page=3;
	
	public $images_path='/uploads/xlsimport';
	
	public $step=1;
	public $perStep=100;
	public $cached_filename;

	public $clear_catalog=0;
	public $clear_catalog_with_categories=0;
	public $auto=1;

	/**
	 * (non-PHPdoc)
	 * @see \common\components\base\FormModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
			
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
//			['import_filename', 'required', 'except'=>'step'],
			['import_filename', 'file', 'allowEmpty'=>false, 'except'=>'step'],
			['sheet_page', 'numerical', 'integerOnly'=>true],
			['images_path, step, cached_filename', 'safe'],
			['clear_catalog, clear_catalog_with_categories, auto', 'boolean']
		]);
	}

	/**
	 * (non-PHPdoc)
	 * @see \CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
			'import_filename'=>'Файл импорта XLS',
			'sheet_page'=>'Номер листа, на котором находится список товаров',
			'clear_catalog'=>'Полностью перезаписать каталог',
			'clear_catalog_with_categories'=>'Удалить все категории',
			'auto'=>'Продолжить в автоматическом режиме'
		]);
	}

	public function import()
	{
		if($this->validate()) {
			$data=$this->prepareData();
			if(!empty($data)) {
				$start=((int)$this->step-1) * (int)$this->perStep;

				if($this->clear_catalog && ($start === 0)) {
					$this->clearCatalog((int)$this->clear_catalog_with_categories);
				}

				$end=((int)$this->step * (int)$this->perStep) - 1;
				$current=-1;
				foreach($data as $row=>$rowData) {
					$current++;
					if($current < $start) continue;
					if($current > $end) {
						return [
							'step'=>$this->step, 
							'next'=>$this->step+1, 
							'current'=>$current, 
							'start'=>$start,
							'count'=>$current-$start, 
							'total'=>count($data),
							'last'=>ceil(count($data) / (int)$this->perStep)
						];
					}
					// 0 - код товара
					// 1 - наименование
					// 2 - раздел
					// 3 - доллар
					// 4 - евро
					// 5 - рубль
					// 6 - фото
					if(!($product=Product::model()->wcolumns(['code'=>$rowData[0]])->find())) {
						$product=new Product;
						$product->code=$rowData[0];
					}
					$product->title=$rowData[1];
					$this->setAlias($product, $rowData[1]);
					$this->setCategoryId($product, $rowData[2]);
					
					if(is_numeric($rowData[5])) {
						$product->price_origin=$this->normalizePrice($rowData[5]);
					}
					elseif(is_numeric($rowData[3])) {
						$product->price_origin=$this->normalizePrice($rowData[3]);
						$product->price_usd=$this->normalizePrice($rowData[3]);
						$product->currency=HCurrency::USD;
					}
					elseif(is_numeric($rowData[4])) {
						$product->price_origin=$this->normalizePrice($rowData[4]);
						$product->price_eur=$this->normalizePrice($rowData[4]);
                        $product->currency=HCurrency::EUR;
					}
					
					if(!$product->save()) {
						$this->addError('import_filename', var_export($product->getErrors(), true));
					}
					else {
						$this->copyProductImage($product, $rowData[6]);
						$product->update(['main_image']);
					}
				}
				unlink($this->getCachedFullname());
				return true;
			}
		}

		return false;
	}

	protected function normalizePrice($price)
	{
		return (float)str_replace(',','.',$price);
	}
	
	protected function copyProductImage($product, $filename)
	{
		$fullname=Yii::getPathOfAlias('webroot') . '/' . trim($this->images_path, '/') . '/' . $filename;
		if(is_file($fullname)) {
			$productImageFilename=$product->mainImageBehavior->getBasename() . '.' . pathinfo($fullname, PATHINFO_EXTENSION);
			if(copy($fullname, Y::getPathOfAlias('webroot.images.product').'/'.$productImageFilename)) {
				$product->main_image=$productImageFilename;
			}
		}
	}

	protected function setAlias(&$model, $alias)
	{
		$alias=HTools::alias($alias);
		if($existsModel=$model->wcolumns(['alias'=>$alias])->find()) {
			if($existsModel->id != $model->id) {
				$alias=$alias . HHash::u('_');
			}
		}
		$model->alias=$alias;
	}
	
	protected function setCategoryId(&$product, $categoryPath)
	{
		if($categoryPath) {
			$paths=explode(';', $categoryPath);
			$categoryTitle=array_shift($paths);
			if(preg_match('/Каталог/i', $categoryTitle)) {
				if(empty($paths)) return;
				$categoryTitle=array_shift($paths);
			}
			if(!($rootCategory=Category::model()->roots()->wcolumns(['title'=>$categoryTitle])->find())) {
				$rootCategory=new Category;
				$rootCategory->title=$categoryTitle;
				$this->setAlias($rootCategory, $categoryTitle);
				$rootCategory->saveNode();
			}
			
			if(empty($paths)) {
				$product->category_id=$rootCategory->id;
			}
			
			$parentCategory=$rootCategory;
			while($categoryTitle=array_shift($paths)) {
				if(!trim($categoryTitle)) continue;
				if(!($category=Category::model()->wcolumns(['title'=>$categoryTitle])->find())) {
					$category=new Category;
	                $category->title=$categoryTitle;
					$this->setAlias($category, $categoryTitle);
                    $category->appendTo($parentCategory);
				}
				$product->category_id=$category->id;
				$parentCategory=$category;
			}
		}
	}

	protected function getCachedFullname()
	{
		return Yii::getPathOfAlias('application.runtime.xlsimport') . '/' . $this->cached_filename;
	}

	protected function prepareData()
	{
		$data=[];

		$importFile=\CUploadedFile::getInstance($this, 'import_filename');
		if($importFile instanceof \CUploadedFile) {
			$this->cached_filename='xlsimport__'.date('Ymd_His').'__'.md5($importFile->getName()) . '.' . pathinfo($importFile->getName(), PATHINFO_EXTENSION);
			$cachedFilename=$cachedPath.$this->cached_filename;
			$importFile->saveAs($this->getCachedFullname());
		}

		$cachedFilename=$this->getCachedFullname();
		if(is_file($cachedFilename)) {
			try {
                $inputFileType = \PHPExcel_IOFactory::identify($cachedFilename);
			    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
			    $objPHPExcel = $objReader->load($cachedFilename);
			    
			    $sheet = $objPHPExcel->getSheet($this->sheet_page-1); 
				$highestRow = $sheet->getHighestRow(); 
				$highestColumn = $sheet->getHighestColumn(); 
				for ($row=3; $row<=$highestRow; $row++) { 
    				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
    				if(isset($rowData[0][0])) {
    					for($col=0; $col<count($rowData[0]); $col++) {
							$data[$row][$col]=$rowData[0][$col];
	    				}
    				}
				}
			} catch(Exception $e) {
			    $this->addError('import_filename', 'Error loading file "'.pathinfo($importFile->getName(),PATHINFO_BASENAME).'": '.$e->getMessage());
			}
		}

		return $data;
	}

	/**
	 * Полная очистка каталога от категорий и товаров.
	 * Дополнительно удаляются все файлы и изображения.
	 * @return boolean
	 */
	protected function clearCatalog($clearCategory=false)
	{
		$query='DELETE FROM `product`;'
			. 'DELETE FROM `related_category`;'
			. 'DELETE FROM `image` WHERE `model`=\'product\';'
            . 'DELETE FROM `file` WHERE `model`=\'product\';'
            . 'DELETE FROM `metadata` WHERE `owner_name`=\'product\';'
            . 'ALTER TABLE `product` AUTO_INCREMENT = 1;';
		
		if($clearCategory) {
			$query.='DELETE FROM `category`;'
				. 'DELETE FROM `image` WHERE `model`=\'category\';'
				. 'DELETE FROM `file` WHERE `model`=\'category\';'
				. 'DELETE FROM `metadata` WHERE `owner_name`=\'category\';'
				. 'ALTER TABLE `category` AUTO_INCREMENT = 1;';
		}
		HDb::execute($query);

		foreach(['image', 'file', 'metadata'] as $tableName) {
			$maxId=(int)HDb::queryScalar('SELECT MAX(`id`) FROM `image`') + 1;
			HDb::execute("ALTER TABLE `{$tableName}` AUTO_INCREMENT = {$maxId};");
		}

		// удаление картинок и файлов
		foreach(['images', 'files'] as $alias) {
			$dirs=['product'];
			if($clearCategory) $dirs[]='category';
			foreach($dirs as $subalias) {
				$path=\Yii::getPathOfAlias("webroot.{$alias}.{$subalias}");
				if(is_dir($path)) {
					if($files=HFile::getFiles($path, true)) {
						array_map('unlink', $files);
					}
				}
			}
		}

		return true;

	}	
}
