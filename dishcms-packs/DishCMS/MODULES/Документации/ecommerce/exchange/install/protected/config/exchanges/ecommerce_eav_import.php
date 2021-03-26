<?php
use common\components\helpers\HArray as A;
use common\components\helpers\HTools;
use common\components\helpers\HDb;

return [
    /**
     * @param \common\ext\iterator\models\Process $iteratorProcess объект процесса
     * @param [] $data данные для импорта.
     * Поля файла импорта:
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
     *       N: Описание (поля в файле выгрузке может не быть)
     *       ... Атрибуты (заголовок является наименованием атрибута) ...
     * @param [] $headers заголовки
     * @param [] $info дополнительная информация вида array(
     *  'spreadsheet'=>\PhpOffice\PhpSpreadsheet\Reader\Xlsx,
     *  'max_columns'=>максимальное количество колонок (буква),
     *  'max_rows'=>максимальное количество строк в файле,
     *  'begin_row'=>номер строки начала выборки данных текущей итерации,
     *  'end_row'=>номер строки окончания выборки данных текущей итерации,
     *  'iteration'=>номер текущей итерации,
     *  'limit'=>лимит записей текущей итерации
     * )
     */
    'data'=>function($iteratorProcess, $data, $headers, $info) {
        set_time_limit(0);

        static $aliasPostfixs=[];
        $fGetAlias=function($alias, $tableName) use (&$aliasPostfixs) {
            if(!isset($aliasPostfixs[$tableName][$alias])) {
                $aliasPostfixs[$tableName][$alias]=1;
            }

            $lastAliases=HDb::queryColumn('SELECT `alias` 
                FROM ' . HDb::qt($tableName) . ' 
                WHERE `alias` REGEXP \'^' . $alias . '-[0-9]+$\''
            );

            if(!empty($lastAliases)) {
                $lastAliasPostfix=0;
                foreach($lastAliases as $lastAlias) {
                    if(preg_match('/^(.*?)-([0-9]+)$/', $lastAlias, $m)) {
                        if((int)$m[2] > $lastAliasPostfix) {
                            $lastAliasPostfix=(int)$m[2];
                        }
                    }
                }

                if($lastAliasPostfix > $aliasPostfixs[$tableName][$alias]) {
                    $aliasPostfixs[$tableName][$alias]=$lastAliasPostfix + 1;
                }
                else {
                    $aliasPostfixs[$tableName][$alias]++;
                }
            }
            
            if($aliasPostfixs[$tableName][$alias] > 1) {
                $newAlias="{$alias}-" . $aliasPostfixs[$tableName][$alias];
                $aliasPostfixs[$tableName][$alias]++;
                $alias=$newAlias;
            }
            else {
                $aliasPostfixs[$tableName][$alias]++;
            }

            return $alias;
        };

        $fCreateCategory=function($path, $parent=null) use (&$fCreateCategory, &$fGetAlias) {
            if($title=array_shift($path)) {
                if(!$parent && in_array($title, ['Домой', 'Продукция'])) {
                    return $fCreateCategory($path);
                }
                
                if($parent instanceof \Category) {
                    $category=$parent->children()->findByAttributes(['title'=>$title]);
                }
                else {
                    $category=\Category::model()->roots()->findByAttributes(['title'=>$title]);
                }
                
                if(!$category) {
                    $category=new \Category;
                    $category->title=$title;
                    $category->alias=$fGetAlias(HTools::alias($title), 'category');
                    
                    if($parent instanceof \Category) {
                        $category->appendTo($parent);
                    }
                    else {
                        $category->saveNode();
                    }

					if($category->id) {
						$category=\Category::model()->findByPk($category->id);
					}
                }
                
                if(!empty($path) && $category->id) {
                    return $fCreateCategory($path, $category);
                }
                elseif($category->id) {
                    return $category->id;
                }
            }
            elseif($parent) {
                return $parent->id;
            }
            
            return null;
        };
        
        $hasDescriptionColumn=(!empty($headers['N']) && (trim($headers['N']) == 'Описание'));
        
        $eavAttributes=A::toa(\EavAttribute::model()->findAll());
        $eavAttributesColumnOffset=$hasDescriptionColumn ? 14 : 13;
        $eavAttributeHeaders=[];
        $eavAttributeRawHeaders=array_slice($headers, $eavAttributesColumnOffset);
        foreach($eavAttributeRawHeaders as $col=>$header) {
            $header=trim($header);
            foreach($eavAttributes as $eavAttribute) {
                if($eavAttribute->name == $header) {
                    $eavAttributeHeaders[$col]=$eavAttribute->id;
                    break;
                }
            }
            
            if(empty($eavAttributeHeaders[$col])) {
                $eavAttribute=new \EavAttribute();
                $eavAttribute->name=$header;
                if($eavAttribute->save()) {
                    $eavAttributeHeaders[$col]=$eavAttribute->id;
                }
            }
        }
        
        $eavAttributeInsertValues=[];
        $eavDeleteValueExpressions=[];
        
        $failed=[];
        foreach($data as $item) {
            $xmlId=trim($item['A']);
            $title=trim($item['B']);
            
            if(!$xmlId) {
                $xmlId=md5($title);
            }
            
            $product=\Product::model()->findByAttributes(['xml_id'=>$xmlId]);
            if(!$product) {
                // $product=\Product::model()->findByAttributes(['title'=>$title]);
            }
            
            if(!$product) {
                $product=new \Product;
                $product->xml_id=$xmlId;
                $product->alias=$fGetAlias(HTools::alias($title), 'product');
            }
            else {
                $product->alias=$fGetAlias($product->alias, 'product');
            }
            
            $product->title=$title;
            $product->category_id=$fCreateCategory(array_slice($item, 6, 6));
            $product->price1=$item['E'];
            $product->price2=$item['F'];
            $product->meta_title=$item['C'];
            $product->meta_desc=$item['D'];
            
            if($hasDescriptionColumn) {
                $product->description=$item['N'];
            }
            
            if($product->save()) {
                // сохранение изображения
                if(!empty($item['M'])) {
                    $webroot=\Yii::getPathOfAlias('webroot');
                    $imageFile="{$webroot}/upload/images/{$item['M']}";
                    if(is_file($imageFile)) {
                        $ext=end(explode('.', $item['M']));
                        @copy($imageFile, "{$webroot}/images/product/{$product->id}.{$ext}");
                    }
                }
                
                // выгрузка атрибутов товара
                $itemEavAttributes=array_slice($item, $eavAttributesColumnOffset);
                if(!empty($itemEavAttributes)) {                                        
                    foreach($eavAttributeHeaders as $col=>$id) {
                        $value=trim($item[$col]);
                        if($value !== '') {
                            $eavDeleteValueExpressions[]="(`id_attrs`={$id} AND `id_product`={$product->id})";
                            if($value !== '---') {
                                $eavAttributeInsertValues[]=[
                                    'id_attrs'=>$id, 
                                    'id_product'=>$product->id, 
                                    'value'=>$value,
                                    'enum_value'=>(float)$value
                                ];
                            }
                        }
                    }
                }
            }
            else {
		$errorMsgs=[];
		foreach($product->getErrors() as $err) {
			$errorMsgs[]=is_array($err) ? implode(', ', $err) : $err;
		}
                $failed[]="{$product->xml_id}: {$product->title} (" . implode(', ', $errorMsgs) . ')';
            }
        }
        
        if(!empty($eavDeleteValueExpressions)) {
            HDb::query('DELETE FROM `eav_value` WHERE ' . implode(' OR ', $eavDeleteValueExpressions));
        }
        
        if(!empty($eavAttributeInsertValues)) {
            HDb::insert('eav_value', $eavAttributeInsertValues);
        }
        
        $message='Идет выгрузка товаров. Выгружено ' . ($info['end_row'] - 2) . ' из ' . ($info['max_rows'] - 2);
        
        if(!empty($failed)) {
            $message.='<br/><strong>Следующие товары не удалось выгрузить:</strong><br/>' . implode('<br/>', $failed);
        }
        
        $iteratorProcess->setParam('message', $message);
    }
];
