<?php
/**
 * Экспорт данных модели \Product
 */
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use PhpOffice\PhpSpreadsheet\Helper\Html as HtmlHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

return [
    /**
     * @param \common\ext\iterator\models\Process $iteratorProcess объект процесса
     * @param [] $info дополнительная информация вида array(
     *  'filename'=>имя файла для экспорта,
     *  'iteration'=>номер текущей итерации,
     *  'limit'=>лимит записей текущей итерации
     * )
     */
    'data'=>function($iteratorProcess, $info) {
        $filename=$info['filename'];
        $iteration=$info['iteration'];
        $limit=$info['limit'];
        $offset=$iteration * $limit;
        $total=\Product::model()->count();
        
        if(is_file($filename)) {
            $spreadsheet=IOFactory::load($filename);            
        }
        else {
            $spreadsheet=new Spreadsheet();
            $spreadsheet->getProperties()->setCreator(\Yii::app()->name)
                ->setLastModifiedBy(\Yii::app()->name)
                ->setTitle('Выгрузка товаров с сайта ' . $_SERVER['SERVER_NAME'] . ' от ' . date('d.m.Y'))
                ->setSubject('')
                ->setDescription('')
                ->setKeywords('')
                ->setCategory('');
        }
        
        $spreadsheet->setActiveSheetIndex(0);
        $activeSheet=$spreadsheet->getActiveSheet();
        
        /*
         * Поля файла экспорта:
         *       A: Артикул
         *       B: Наименование
         *       C: Категория
         *       D: Подкатегория
         *       E: Цена
         *       F: Остаток
         */
        
        if(!$offset) {
            $headers=[
                'Артикул',
                'Наименование',
                'Категория',
                'Подкатегория',
                'Цена (руб)',
                'Остаток (шт)'
            ];

            $sizes=[15, 50, 35, 35, 15, 15];
            
            $activeSheet->fromArray($headers, null, 'A1');
            $activeSheet->freezePane('A2');
            
            $maxCol=Coordinate::stringFromColumnIndex(count($headers));
            
            $activeSheet->getStyle("A1:{$maxCol}1")->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
            $activeSheet->getStyle("A1:{$maxCol}1")->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
            $activeSheet->getStyle("A1:{$maxCol}1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFFCC');
            
            foreach($headers as $idx=>$header) {
                $col=Coordinate::stringFromColumnIndex($idx+1);
                if(isset($sizes[$idx])) {
                    $activeSheet->getColumnDimension($col)->setWidth($sizes[$idx]);
                }
                else {
                    $activeSheet->getColumnDimension($col)->setAutoSize(true);                
                }
            }
        }        
        
        $cacheCategories=[];
        $wizard=new HtmlHelper();

        $criteria=new \CDbCriteria;
        $criteria->addCondition('(NOT ISNULL(`code`)) AND (LENGTH(`code`) > 0)');
        // $criteria->addCondition('(`quantity` > 0) AND (NOT ISNULL(`code`)) AND (LENGTH(`code`) > 0)');
        $criteria->limit=$limit;
        $criteria->offset=$offset;
        $criteria->order='`category_id`, `title`, `id`';
        $criteria->select='`id`, `category_id`, `title`, `code`, `price`, `quantity`';

        if($products=\Product::model()->findAll($criteria)) {
            $data=[];
            $startRow=$offset + 2;
            $row=$startRow;
            foreach($products as $product) {
                if(empty($cacheCategories[$product->category_id])) {
                    $cacheCategory=['title'=>'Без категории', 'parent'=>null];
                    if($category=\Category::model()->findByPk($product->category_id, ['select'=>'id, title, lft, rgt, level, root'])) {
                        $cacheCategory['title']=trim($category->title);
                        if($ancestors=$product->category->ancestors()->findAll(['select'=>'id, title, lft, rgt, level, root'])) {
                            $child=&$cacheCategory['parent'];
                            foreach($ancestors as $ancestor) {
                                $child=['title'=>trim($ancestor->title), 'parent'=>null];
                                $child=&$child['parent'];
                            }
                        }
                    }
                    $cacheCategories[$product->category_id]=$cacheCategory;
                }

                $category=$cacheCategories[$product->category_id];

                $data[$row]=[
                    trim($product->code),
                    trim($product->title),
                    empty($category['parent']) ? $category['title'] : $category['parent']['title'],
                    empty($category['parent']) ? '' : $category['title'],
                    $product->price,
                    $product->quantity
                ];
                
                $row++;
            }
            
            $activeSheet->fromArray($data, null, 'A' . $startRow);
            $activeSheet->getStyle("A{$startRow}:" . Coordinate::stringFromColumnIndex(count($data)) . ($row - 1))->getAlignment()->setWrapText(false);

            // сохраняем данные в файл
            $writer=IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($filename);
        }
        
        $count=($iteration + 1) * $limit;
        if($count > $total) {
            $count=$total;
        }
        
        $iteratorProcess->setParam('message', "Идет выгрузка товаров. Обработано {$count} из {$total}");
        
        // возвращаем процент завершенности операции
        $percent=floor($count * 100 / $total);
        return $percent ?: 1;
    }
];
