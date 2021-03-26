<?php
/**
 * Export Catalog to Excel file
 *
 * @use PHPExcel
 */
namespace kontur\export;

define('MANUFACTURER_HL_ID', 4);
define('PRICE_TYPE_ID', 1);
define('DETAIL_PAGE_URL_PREFIX', 'http://lorder.ru');
define('KONTUR_EXCEL_FILENAME', '/upload/pricelist/lorder.xls');

set_time_limit(0);

$_SERVER['DOCUMENT_ROOT']='/home/bitrix/www';
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

set_include_path(get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/kontur/vendors/PHPExcel/Classes');

require_once('PHPExcel.php');

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('catalog');

Excel::run(15, $_SERVER['DOCUMENT_ROOT'].KONTUR_EXCEL_FILENAME, include('properties.php'));

class Excel 
{
	protected $iblockId;
	protected $filename;

	// @var array element properties
	protected $properties;

	// @var array id catalog available
	protected $available=array();

	// @var array product quantity
	protected $quantity=array();

	// @var array product prices
	protected $prices=array();
    
    // @var array data
    protected $data=array();

    protected $styles=array();

	public static function run($IBLOCK_ID, $FILENAME, $arProperties=array())
	{
		$self=new self();
		$self->init($IBLOCK_ID, $FILENAME, $arProperties);
		$self->prepareData();
        $self->save();
	}

	public function init($IBLOCK_ID, $FILENAME, $arProperties=array())
	{
		if(empty($IBLOCK_ID) || empty($FILENAME)) {
			throw \Exception('Error: $IBLOCK_ID or $FILENAME values is empty!');
		}

		$this->iblockId=$IBLOCK_ID;
		$this->filename=$FILENAME;
		
        $this->data=array();

		$this->initCatalog();
		$this->initProperties($arProperties);
	}

	protected function initCatalog()
	{
		$rs=\Bitrix\Catalog\ProductTable::getList(array(
			'select'=>array('ID', 'QUANTITY', 'QUANTITY_TRACE', 'CAN_BUY_ZERO', 'AVAILABLE'),
			'filter'=>array('AVAILABLE'=>'Y'),
		));
		while($row=$rs->fetch()) {
			$this->available[$row['ID']]=1;
			$this->quantity[$row['ID']]=$row['QUANTITY'];
			$this->prices[$row['ID']]=$this->getPrice($row['ID']);
		}
	}

	protected function getPrice($ID)
	{
		$rs=\CPrice::GetListEx(
	        array(),
    	    array("PRODUCT_ID" => $ID, "CATALOG_GROUP_ID" => PRICE_TYPE_ID)
	    );
		if($row=$rs->Fetch()) {
			return \CurrencyFormat($row["PRICE"], $row["CURRENCY"]);
		}
		return 0;
	}

	protected function initProperties($arProperties=array())
	{
		$this->properties=array();
		if(!empty($arProperties)) { 
			$this->properties=$arProperties;
		}
	}

	protected function isAvailable($id) 
	{
		return isset($this->available[$id]);
	}

	protected function getSectionList($id=null, $filter=array())
	{
		if($id) $filter['IBLOCK_SECTION_ID']=$id;

		$filter=array_merge(
			array('IBLOCK_ID' => $this->iblockId, 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y'), 
			$filter
		);
	
		return \Bitrix\Iblock\SectionTable::getList(array(
			'select'=>array('ID','NAME'),
			'order'=>array('NAME'),
	    	'filter'=>$filter
    	))->fetchAll();
	}

	protected function getChildren($id=null, $depth=1)
    {
        return $this->getSectionList($id, array('DEPTH_LEVEL'=>$depth));
    }
    
    protected function getElementList($sectionId, $filter=array())
    {
        $filter=array_merge(
            array(
                'IBLOCK_ID' => $this->iblockId,
                'SECTION_ID'=>$sectionId,
                'ACTIVE'=>'Y',
        	   	'GLOABAL_SECTION_ACTIVE'=>'Y'
            ), 
            $filter
        );

        //$arSelect=array_keys($this->properties);
        //array_walk($arSelect, function(&$v) { $v='PROPERTY_'.$v; });
        $arSelect=array();
        $arSelect[]='ID';
        $arSelect[]='IBLOCK_ID';
        $arSelect[]='NAME';
        $arSelect[]='PROPERTY_MERLION_CODE';
        $arSelect[]='DETAIL_PAGE_URL';
        $rs = \CIBlockElement::GetList(array('NAME'=>'ASC'), $filter, false, array(), $arSelect); 

        $arResult=array(); 
        while($row=$rs->GetNext()) { 
            if($this->isAvailable($row['ID'])) {
            	$props=''; 
            	$rsProp=\CIBlockElement::GetProperty($this->iblockId, $row['ID']);
            	while($prop=$rsProp->GetNext()) {
            		if(isset($this->properties[$prop['CODE']]) && $prop['VALUE']) {
            			if($prop['CODE']=='MANUFACTURER') {
            				$hl=\Bitrix\Highloadblock\HighloadBlockTable::getById(MANUFACTURER_HL_ID)->fetch();
            				if(!empty($hl)) {
            					$entity=\Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hl);
            					$entityDataClass = $entity->getDataClass();
            					$entityRow = $entityDataClass::getById($prop['VALUE'])->fetch();
            					if($entityRow) $propValue=$entityRow['UF_NAME'];
            				}
            			}
            			else {
            				$propValue=$prop['VALUE'];
            			}
            			if($propValue) {
            				$props.=$prop['NAME'].': '.$propValue."\n";
	            		}
            		}
            	} 
                $arResult[]=array(
                	$row['PROPERTY_MERLION_CODE_VALUE'], 
                	$row['NAME'], 
                	$this->prices[$row['ID']],
                	$this->quantity[$row['ID']],
                	$props,
                	DETAIL_PAGE_URL_PREFIX.$row['DETAIL_PAGE_URL']
                );
            }
        }
        
        return $arResult;
    }
    
    protected function prepareDataAddRows(&$arRows, $section, $depth, $titlePrefix='')
    {
        if($children=$this->getChildren($section['ID'], $depth)) { 
			foreach($children as $child) {
				if($elements=$this->getElementList($child['ID'])) { 
					$arRows['SECTIONS'][]=array('TITLE'=>$titlePrefix.$child['NAME'], 'ITEMS'=>$elements);
				}
				$this->prepareDataAddRows($arRows, $child, $depth+1, $titlePrefix.$child['NAME'].' / ');
			}
		}  
    }
    
    protected function prepareData()
    {
        if($roots=$this->getChildren()) { 
            foreach($roots as $root) {
                $arRows=array();

                $arRows['ROOT']=array('TITLE'=>$root['NAME'], 'ITEMS'=>$this->getElementList($root['ID']));
		
                $this->prepareDataAddRows($arRows, $root, 2);

                if(!empty($arRows)) {
                    $this->data[]=array('TITLE'=>$root['NAME'], 'ROWS'=>$arRows);
                }
            }
        }
    }

    protected function setExcelDocProperties(&$phpExcel)
    {
        $phpExcel->getProperties()->setCreator('Lorder')
            ->setLastModifiedBy(date('d.m.Y H:i:s'))
            ->setTitle('© Lorder. '.date('d.m.Y'))
            ->setSubject('')
            ->setDescription('')
            ->setKeywords('')
            ->setCategory('');
    }

    protected function setExcelPageSetup(&$activeSheet)
    {
		$activeSheet->getPageSetup()
			->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$activeSheet->getPageSetup()
			->SetPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		
		$activeSheet->getPageMargins()->setTop(1);
		$activeSheet->getPageMargins()->setRight(0.75);
		$activeSheet->getPageMargins()->setLeft(0.75);
		$activeSheet->getPageMargins()->setBottom(1);
    }

    protected function setExcelDefaultStyle(&$phpExcel)
    {
        $phpExcel->getDefaultStyle()->getFont()->setName('Arial');
        $phpExcel->getDefaultStyle()->getFont()->setSize(8);
    }

    public function getExcelStyles()
    {
    	if(count($this->styles)) return $this->styles;
    	else 
    	{
            $this->styles = array();

            $configs = array(
                'default'=>array(
                    'font'=>array('bold'=>false, 'name'=>'Arial', 'size'=>8),
                    'alignment'=>array('wrap'=>true, 'vertical' => \PHPExcel_STYLE_ALIGNMENT::VERTICAL_TOP),
                    'borders'=>array(
                        'bottom' => array('style'=>\PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style'=>\PHPExcel_Style_Border::BORDER_THIN)
                     )
                ),
                'header'=>array(
                    'font'=>array('bold'=>true, 'name'=>'Arial', 'size'=>15, 'color'=>array('rgb' => 'FFFFFF')),
                    'alignment'=>array('vertical' => \PHPExcel_STYLE_ALIGNMENT::VERTICAL_TOP),
                    'fill' => array(
						'type' => \PHPExcel_STYLE_FILL::FILL_SOLID,
						'color'=>array('rgb' => 'DA1D40')
					),
                    'borders' => array(
                        'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color'=>array('rgb' => 'DA1D40'))
                     )
                ),
                'headerRight'=>array(
                    'font'=>array('name'=>'Arial', 'size'=>10, 'color'=>array('rgb' => 'FFFFFF')),
                    'alignment'=>array(
                    	'vertical' => \PHPExcel_STYLE_ALIGNMENT::VERTICAL_BOTTOM,
                    	'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
                    ),
                    'fill' => array(
						'type' => \PHPExcel_STYLE_FILL::FILL_SOLID,
						'color'=>array('rgb' => 'DA1D40')
					),
                    'borders' => array(
                        'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)
                     )
                ),
                'h1'=>array(
                    'font'=>array('bold'=>true, 'name'=>'Arial', 'size'=>12),
                    'alignment' => array('vertical' => \PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER),
                    'borders' => array(
                        'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)
                     )
                ),
                'h2'=>array(
                    'font'=>array('bold'=>true, 'name'=>'Arial', 'size'=>10.5),
                    'alignment' => array('vertical' => \PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER),
                    'borders' => array(
                        'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)
                     )
                ),
                'headings'=>array(
                    'font'=>array('bold'=>true, 'name'=>'Arial', 'size'=>10),
                    'alignment' => array('vertical' => \PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER),
                    'fill' => array(
						'type' => \PHPExcel_STYLE_FILL::FILL_SOLID,
						'color'=>array('rgb' => 'CFCFCF')
					),
                    'borders' => array(
                        'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)
                     )
                )
            );

            foreach($configs as $key=>$config) {
                $style = new \PHPExcel_Style();
                $style->applyFromArray($config);
                $this->styles[$key]=$style;
            }
    	}

    	return $this->styles;
    }

    protected function createExcelSheet(&$phpExcel, &$rows, $title, $idxSheet=0)
    {
    	// $colNames=array('A','B','C','D','E','F','G','H','I','J');
    	$colsHeaders=array('#', 'Артикул', 'Наименование', 'Цена', 'Кол-во', 'Характеристики', 'Ссылка на страницу товара');
        $rows=array();
        $styles=$this->getExcelStyles();

	    if($idxSheet) {
    	    $phpExcel->createSheet();
        }
        $phpExcel->setActiveSheetIndex($idxSheet);

        $activeSheet=$phpExcel->getActiveSheet();
        $this->setExcelPageSetup($activeSheet);

        // Maximum 31 characters allowed in sheet title 
        $title31=iconv('UTF-8', 'windows-1251', $title);
        $title31=preg_replace('/^(.{28})(.{3})(.+)$/', '$1...', $title31);
        $title31=iconv('windows-1251', 'UTF-8', $title31);
        $activeSheet->setTitle($title31);

        
        $rows[]=array("© LORDER\nТовары для офиса и семьи", '', '', '', '', '', date('d.m.Y'));
		$activeSheet->mergeCells("A1:F1");
        $activeSheet->setSharedStyle($styles['header'],'A1:F1');
        $activeSheet->setSharedStyle($styles['headerRight'],'G1');
        $activeSheet->getRowDimension(1)->setRowHeight(40);

        $rows[]=array($title);
		$activeSheet->mergeCells("A2:G2");
        $activeSheet->setSharedStyle($styles['h1'],"A2");
        $activeSheet->getRowDimension(2)->setRowHeight(20);

        $rows[]=array('#', 'Артикул', 'Наименование', 'Цена', 'Кол-во', 'Характеристики', 'Ссылка на страницу товара');
        foreach(array('A'=>7,'B'=>11,'C'=>40,'D'=>19,'E'=>10,'F'=>50,'G'=>35) as $c=>$w) {
	        $activeSheet->getColumnDimension($c)->setWidth($w);
            $activeSheet->setSharedStyle($styles['headings'], $c.'3');
        }
        $activeSheet->getRowDimension(3)->setRowHeight(15);
   		$activeSheet->freezePane('A4');
        
        return $activeSheet;
    }
    
    protected function addExcelSectionRow(&$activeSheet, &$rows, &$n, $items, $title=null)
    {
        $styles=$this->getExcelStyles();
        
        $nrow=count($rows);
        if($title) {
            $rows[]=array($title);
            $nrow++;
            $activeSheet->mergeCells("A{$nrow}:G{$nrow}");
            $activeSheet->getRowDimension($nrow)->setRowHeight(18);
            $activeSheet->setSharedStyle($styles['h2'],'A'.$nrow);
        }
        
        foreach($items as $itemData) {
            array_unshift($itemData, ++$n);
            $rows[]=$itemData;
            $nrow++;
            $activeSheet->getRowDimension($nrow)->setRowHeight(-1);
            $activeSheet->getCell('G'.$nrow)->getHyperlink()->setUrl(end($itemData));
            foreach(array('A','B','C','D','E','F','G') as $c) {
                $activeSheet->setSharedStyle($styles['default'], $c.$nrow);
            }
       		$activeSheet->getStyle("D{$nrow}:E{$nrow}")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        }
    }

    // write to excel file
    protected function save()
    {
        $phpExcel = new \PHPExcel();

        $this->setExcelDocProperties($phpExcel);
        $this->setExcelDefaultStyle($phpExcel);

        $idxSheet=0;
        foreach($this->data as $data) {
            $n=0; // @var integer position number
            $rows=array(); // @var array sheet rows
            
            $activeSheet=$this->createExcelSheet($phpExcel, $rows, $data['TITLE'], $idxSheet);
            
            if(!empty($data['ROWS']['ROOT']['ITEMS'])) {
                $this->addExcelSectionRow($activeSheet, $rows, $n, $data['ROWS']['ROOT']['ITEMS']);
            }
            
            if(!empty($data['ROWS']['SECTIONS'])) {
                foreach($data['ROWS']['SECTIONS'] as $sectionData) {
                    $this->addExcelSectionRow($activeSheet, $rows, $n, $sectionData['ITEMS'], $sectionData['TITLE']);
                }
            }
            
        	$activeSheet->fromArray($rows, NULL, 'A1');        	
            
            $idxSheet++;
        }

        $phpExcel->setActiveSheetIndex(0);       

        \PHPExcel_Settings::setLocale('ru');
        $writer = \PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5'); //'Excel2007');
        $writer->save($this->filename);
    }
}