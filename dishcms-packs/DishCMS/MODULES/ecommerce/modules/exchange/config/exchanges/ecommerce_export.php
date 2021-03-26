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
         *       A: ID (xml_id)
         *       B: Наименование
         *       C: Тайтл
         *       D: Дескрипшин
         *       E: Розница
         *       F: Опт
         *       G: Группа 1
         *       H: Группа 2
         *       I: Группа 3
         *       J: Группа 4
         *       K: Группа 5
         *       L: Группа 6
         *       M: Изображение
         *       N: Описание
         *       ... Атрибуты (заголовок является наименованием атрибута) ...
         */
        
        $eavAttributes=HDb::queryAll('SELECT `id`, `name` FROM `eav_attribute` WHERE LENGTH(`name`)>0 AND NOT ISNULL(`name`) ORDER BY `name`');
        $eavAttributeOffset=14;
        $eavAttributeOffsets=[];
        if(!empty($eavAttributes)) {
            $eavAttributeTmpOffset=$eavAttributeOffset;
            foreach($eavAttributes as $eavAttribute) {
                $eavAttributeOffsets[(int)$eavAttribute['id']]=$eavAttributeTmpOffset;
                $eavAttributeTmpOffset++;
            }
        }
        
        if(!$offset) {
            $headers=[
                'ID',
                'Наименование',
                'Тайтл',
                'Дескрипшин',
                'Розница',
                'Опт',
                'Группа 1',
                'Группа 2',
                'Группа 3',
                'Группа 4',
                'Группа 5',
                'Группа 6',
                'Изображение',
                'Описание'
            ];
            
            
            if(!empty($eavAttributes)) {
                foreach($eavAttributes as $eavAttribute) {
                    $headers[]=$eavAttribute['name'];
                }
            }
            
            $activeSheet->fromArray($headers, null, 'A1');
            $activeSheet->freezePane('A2');
            
            $maxCol=Coordinate::stringFromColumnIndex(count($header));
            
            $activeSheet->getStyle("A1:{$maxCol}1")->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
            $activeSheet->getStyle("A1:{$maxCol}1")->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
            $activeSheet->getStyle("A1:{$maxCol}1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFFCC');
            
            foreach($headers as $idx=>$header) {
                $col=Coordinate::stringFromColumnIndex($idx+1);
                if(in_array($idx, [2,3,13])) {
                    $activeSheet->getColumnDimension($col)->setWidth(150);
                }
                else {
                    $activeSheet->getColumnDimension($col)->setAutoSize(true);                
                }
            }
        }        
        
        $cacheCategory=[];
        $wizard=new HtmlHelper();
        if($products=\Product::model()->findAll(['limit'=>$limit, 'offset'=>$offset])) {
            $data=[];
            $startRow=$offset + 2;
            $row=$startRow;
            foreach($products as $product) {
                if(!$product->xml_id) {
                    $product->xml_id=md5($product->title);
                    $product->update(['xml_id']);
                }
                
                $item=[
                    $product->xml_id,
                    $product->title,
                    $product->meta_title,
                    $product->meta_desc,
                    $product->price1,
                    $product->price2
                ];
                
                // устанавливаем категории товара
                if(empty($cacheCategory[$product->category_id])) {
                    $categoryData=[];
                    if($product->category_id && ($category=$product->getRelated('category'))) {
                        if($ancestors=$product->category->ancestors()->findAll(['select'=>'id, title, lft, rgt, level, root'])) {
                            foreach($ancestors as $ancestor) {
                                $categoryData[]=$ancestor->title;
                            }
                        }
                        $categoryData[]=$category->title;
                    }
                    $categoryData=A::m($categoryData, array_fill(count($categoryData) - 1, 6 - count($categoryData), ''));
                    $cacheCategory[$product->category_id]=$categoryData;
                }
                $item=A::m($item, $cacheCategory[$product->category_id]);
                
                // изображение при экспорте задаем пустым
                $item[]='';
                
                // описание товара добавляем как объект RichText
                // $data[]=$wizard->toRichTextObject($product->description);
                $item[]=$product->description;
                
                // заполняем атрибуты
                if(!empty($eavAttributes)) {
                    $item=A::m($item, array_fill($eavAttributeOffset, count($eavAttributes), ''));
                    $eavValues=HDb::queryAll('SELECT `id_attrs`, `value` FROM `eav_value` WHERE `id_product`=:id ORDER BY `id_attrs`', ['id'=>$product->id]);
                    if(!empty($eavValues)) {
                        foreach($eavValues as $eavValue) {
                            if(!empty($eavAttributeOffsets[(int)$eavValue['id_attrs']])) {
                                $item[$eavAttributeOffsets[(int)$eavValue['id_attrs']]]=$eavValue['value'];
                            }
                        }
                    }
                }
                
                $data[$row]=$item;
                
                $row++;
            }
            
            $activeSheet->fromArray($data, null, 'A' . $startRow);
            $activeSheet->getStyle("A{$startRow}:" . Coordinate::stringFromColumnIndex(count($data)) . ($row - 1))->getAlignment()->setWrapText(true);
            
            // сохраняем данные в файл
            $writer=IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($filename);
        }
        
        $count=($iteration + 1) * $limit;
        if($count > $total) {
            $count=$total;
        }
        
        $iteratorProcess->setParam('message', "Идет выгрузка товаров. Выгружено {$count} из {$total}");
        
        // возвращаем процент завершенности операции
        $percent=floor($count * 100 / $total);
        return $percent ?: 1;
    }
];