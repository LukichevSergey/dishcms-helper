<?php
use common\components\helpers\HArray as A;
use common\components\helpers\HTools;
use common\components\helpers\HDb;

return [
    /**
     * @param \common\ext\iterator\models\Process $iteratorProcess объект процесса
     * @param [] $data данные для импорта.
     * Поля файла импорта:
     *       0 A: ID (xml_id)
     *       1 B: Наименование
     *       2 C: Тайтл
     *       3 D: Дескрипшин
     *       4 E: Цена
     *       5 F: Старая цена
     *       6 G: Группа 1
     *       7 H: Группа 2
     *       8 I: Группа 3
     *       9 J: Группа 4
     *       10 K: Группа 5
     *       11 L: Группа 6
     *       12 M: Изображение
     *       13 N: Описание (поля в файле выгрузке может не быть)
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

        // static $aliasPostfixs=[];
        // $fGetAlias=function($alias, $tableName) use (&$aliasPostfixs) {
        //     if(!isset($aliasPostfixs[$tableName][$alias])) {
        //         $aliasPostfixs[$tableName][$alias]=1;
        //     }
        //
        //     $lastAliases=HDb::queryColumn('SELECT `alias`
        //         FROM ' . HDb::qt($tableName) . '
        //         WHERE `alias` REGEXP \'^' . $alias . '-[0-9]+$\''
        //     );
        //
        //     if(!empty($lastAliases)) {
        //         $lastAliasPostfix=0;
        //         foreach($lastAliases as $lastAlias) {
        //             if(preg_match('/^(.*?)-([0-9]+)$/', $lastAlias, $m)) {
        //                 if((int)$m[2] > $lastAliasPostfix) {
        //                     $lastAliasPostfix=(int)$m[2];
        //                 }
        //             }
        //         }
        //
        //         if($lastAliasPostfix > $aliasPostfixs[$tableName][$alias]) {
        //             $aliasPostfixs[$tableName][$alias]=$lastAliasPostfix + 1;
        //         }
        //         else {
        //             $aliasPostfixs[$tableName][$alias]++;
        //         }
        //     }
        //
        //     if($aliasPostfixs[$tableName][$alias] > 1) {
        //         $newAlias="{$alias}-" . $aliasPostfixs[$tableName][$alias];
        //         $aliasPostfixs[$tableName][$alias]++;
        //         $alias=$newAlias;
        //     }
        //     else {
        //         $aliasPostfixs[$tableName][$alias]++;
        //     }
        //
        //     return $alias;
        // };

		$fGetCategoryTree=function() {
			$tree=[];
			if($categories=\Category::model()->findAll(['select'=>'id, title, root, level', 'order'=>'root, lft'])) {
				$current=null; // ссылка на текущую обрабатываемую категорию
				$level=1;
				foreach($categories as $category) {
                    $data=['id'=>$category->id, 'model'=>$category, 'parent'=>null, 'children'=>[]];
                    $title=trim($category->title);
					if($category->root === $category->id) {
						$tree[$title]=$data;
						$current=&$tree[$title];
					}
					else {
						if($category->level > $level) {
							$data['parent']=&$current;
							$current['children'][$title]=$data;
							$current=&$current['children'][$title];
						}
						elseif($category->level < $level) {
							while($category->level <= $level) {
								if(!$current['parent']) break;
								$current=&$current['parent'];
								$level--;
	                        }
	                        $data['parent']=$current;
	                        $current['children'][$title]=$data;
						}
						else {
							$data['parent']=$current;
							$current['parent']['children'][$title]=$data;
						}
					}
					$level=$category->level;
				}
			}
			return $tree;
		};
		
		$tree=$fGetCategoryTree();
		
		$fCreateCategory=function($path) use (&$fCreateCategory, &$tree, &$iteratorProcess) {
			$path=array_filter($path, function($chunk){ return mb_strlen(trim($chunk)) > 0; });
			$_path=$path; $_tree=$tree; $ltree=null; $id=null; $parent=null;
			while($title=trim(array_shift($_path))) {
        		if(!isset($_tree[$title])) break;
        		$id=$_tree[$title]['id'];
        		$parent=$_tree[$title]['model'];
    			$_tree=$_tree[$title]['children'];
    			if($ltree === null) $ltree=&$tree[$title];
    			else $ltree=&$ltree[$title];
    		}
    		
    		if(empty($_path)) {
    			return $id;
    		}
    		elseif(!empty($title)) {
                $category=new \Category;
                $category->title=$title;
                $category->alias=HTools::alias($title);

                if($parent instanceof \Category) {
                    $category->appendTo($parent);
                }
                else {
                    $category->saveNode();
                }

				if($category->id) {
					$ltree['children'][$title]=[
						'id'=>$category->id, 
						'model'=>$category, 
						'parent'=>&$ltree, 
						'children'=>[]
					];
				}
				
				if(!empty($_path)) {
					return $fCreateCategory($path);
				}
				
				return $category->id;
            }

            return null;
        };

        $hasDescriptionColumn=true; // (!empty($headers[13]) && (trim($headers[13]) == 'Описание'));
		$eavAttributes=A::toa(\EavAttribute::model()->findAll());
        $eavAttributesColumnOffset=14; // $hasDescriptionColumn ? 14 : 13;
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
        $productData=[];
        $metaData=[];
        foreach($data as $idx1=>$item) {
            $xmlId=trim($item[0]);
            $title= trim(preg_replace("/[\n\r]+/", ' ', $item[1]));
            
            if(empty($title)) continue;

            $categoryId = $fCreateCategory(array_slice($item, 6, 6));
            if(!$xmlId) {
                $xmlId=md5($title.$categoryId);
            }

            $productData[$xmlId]=[
                'xml_id'=>$xmlId,
                'title'=>$title,
                'alias'=>HTools::alias($title),
                'category_id'=>$categoryId,
                'price'=>$item[4],
                'old_price'=>$item[5],
                'main_image'=>null,
                'description'=>null,
                'update_time'=>date('Y-m-d H:i:s')
            ];

            $metaData[$xmlId]=[
                'owner_name'=>'product',
                'owner_id'=>null,
                'meta_title'=>$item[2] ?: '',
                'meta_desc'=>$item[3] ?: '',
            ];

            if($hasDescriptionColumn) {
                $productData[$xmlId]['description']=$item[13];
            }

            // сохранение изображения
            if(!empty($item[12])) {
                $webroot=\Yii::getPathOfAlias('webroot');
                $imageFile="{$webroot}/upload/images/{$item[12]}";
                if(is_file($imageFile)) {
                    $ext=end(explode('.', $item[12]));
                    $name=md5($item[12]);
                    if(@copy($imageFile, "{$webroot}/images/product/{$name}.{$ext}")) {
                        $productData[$xmlId]['main_image']="{$name}.{$ext}";
                    }
                    else {
                        $productData[$xmlId]['main_image']='';
                    }
                }
            }

            // выгрузка атрибутов товара
            $itemEavAttributes=array_slice($item, $eavAttributesColumnOffset);
            if(!empty($itemEavAttributes)) {
                foreach($eavAttributeHeaders as $col=>$id) {
                    $value=trim($item[$col + $eavAttributesColumnOffset]);
                    if($value !== '') {
                        if(empty($eavDeleteValueExpressions[$xmlId])) {
                            $eavDeleteValueExpressions[$xmlId]=[];
                        }
                        $eavDeleteValueExpressions[$xmlId][]="(`id_attrs`={$id} AND `id_product`=:productId)";
                        if($value !== '---') {
                            if(empty($eavAttributeInsertValues[$xmlId])) {
                                $eavAttributeInsertValues[$xmlId]=[];
                            }
                            $eavAttributeInsertValues[$xmlId][]=[
                                'id_attrs'=>$id,
                                'id_product'=>null,
                                'value'=>$value,
                                // 'enum_value'=>(float)$value
                            ];
                        }
                    }
                }
            }
        }

        if(!empty($productData)) {
            HDb::insert('product', $productData, '
                `category_id`=VALUES(`category_id`),
                `title`=VALUES(`title`),
                `price`=VALUES(`price`),
                `old_price`=VALUES(`old_price`),
                `main_image`=IF(ISNULL(VALUES(`main_image`)), `main_image`, VALUES(`main_image`)),
                `description`=IF(ISNULL(VALUES(`description`)), `description`, VALUES(`description`)),
                `update_time`=NOW()
            ');

            $productIds=HDb::listData('product', 'id', 'xml_id', [
                'where'=>'`xml_id` IN (\'' . implode('\', \'', array_keys($productData)) . '\')'
            ]);

            foreach($metaData as $xmlId=>$dummy) {
                if(!isset($productIds[$xmlId])) {
                    unset($metaData[$xmlId]);
                }
            }

            foreach($eavDeleteValueExpressions as $xmlId=>$dummy) {
                if(!isset($productIds[$xmlId])) {
                    unset($eavDeleteValueExpressions[$xmlId]);
                }
            }

            foreach($eavAttributeInsertValues as $xmlId=>$dummy) {
                if(!isset($productIds[$xmlId])) {
                    unset($eavAttributeInsertValues[$xmlId]);
                }
            }

            // Обновление META
            foreach($productIds as $xmlId=>$productId) {
                if(!empty($metaData[$xmlId])) {
                    $metaData[$xmlId]['owner_id']=$productId;
                }
                if(!empty($eavDeleteValueExpressions[$xmlId])) {
                    foreach($eavDeleteValueExpressions[$xmlId] as $idx=>$expr) {
                        $eavDeleteValueExpressions[$xmlId][$idx]=str_replace(':productId', $productId, $eavDeleteValueExpressions[$xmlId][$idx]);
                    }
                }
                if(!empty($eavAttributeInsertValues[$xmlId])) {
                    foreach($eavAttributeInsertValues[$xmlId] as $idx=>$expr) {
                        $eavAttributeInsertValues[$xmlId][$idx]['id_product']=$productId;
                    }
                }
            }

            HDb::insert('metadata', $metaData, '
                `meta_title`=VALUES(`meta_title`),
                `meta_desc`=VALUES(`meta_desc`)
            ');

            if(!empty($eavDeleteValueExpressions)) {
                $eavDeleteValueExpressions=array_map(function($exprs) { return implode(' OR ', $exprs); }, $eavDeleteValueExpressions);
                HDb::query('DELETE FROM `eav_value` WHERE ' . implode(' OR ', $eavDeleteValueExpressions));
            }
    
            if(!empty($eavAttributeInsertValues)) {
                $eavAttributeValues=[];
                foreach($eavAttributeInsertValues as $data) {
                    foreach($data as $values) {
                        $eavAttributeValues[]=$values;
                    }
                }
                HDb::insert('eav_value', $eavAttributeValues);
            }
        }

        HDb::query("SET @i:=0; SET @a:=''; UPDATE `product` SET `alias`=IF(@a=`alias`,CONCAT(`alias`,'-',@i:=@i+1),IF(@i:=0,0,@a:=`alias`)) ORDER BY `alias`;");
        HDb::query("SET @i:=0; SET @a:=''; UPDATE `category` SET `alias`=IF(@a=`alias`,CONCAT(`alias`,'-',@i:=@i+1),IF(@i:=0,0,@a:=`alias`)) ORDER BY `alias`;");

        $message='Идет выгрузка товаров. Выгружено ' . ($info['end_row'] - 2) . ' из ' . ($info['max_rows'] - 2);

        if(!empty($failed)) {
            $message.='<br/><strong>Следующие товары не удалось выгрузить:</strong><br/>' . implode('<br/>', $failed);
        }

        $iteratorProcess->setParam('message', $message);
    }
];
