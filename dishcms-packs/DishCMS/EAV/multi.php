use common\components\helpers\HDb;

class Product

 	public static function  eavCriteria($withPrice=false)
    {
        $attributes = Yii::app()->request->getQuery('attr', []);
        if($withPrice) {
            $price_from = (int)Yii::app()->getRequest()->getQuery('price_from');
            $price_to = (int)Yii::app()->getRequest()->getQuery('price_to');
        }

        if ($attributes || $withPrice) {
            $query = 'SELECT `id` FROM (SELECT `p`.`id` AS `id` FROM `product` AS `p`';
            $counter = 0;

            foreach ($attributes as $key => $attributeValues) {
                $conditions = [];
                foreach ($attributeValues as $value => $attributeValue) {
                    if (!$attributeValue) continue;

                    $conditions[] = '(`a`.`value` = "' . $value . '" AND `a`.`id_attrs` = "' . $key . '")';
                }

                if ( !empty($conditions) ) {
                    if ( !$counter ) {
                        $query .= ' LEFT JOIN `eav_value` AS `a` ON (`a`.`id_product`=`p`.`id`) WHERE ';
                    }
                    else {
                        $query .= ' OR ';
                    }
                    $counter++;
                    $query .= implode(' OR ', $conditions);
                }
            }

            if ($withPrice || ($counter > 0)) {

                if($withPrice && ($price_from || $price_to)) {
                    if ( !$counter ) {
                        $query .= ' WHERE';
                    }
                    else {
                        $query .= ' AND';
                    }
                    //Фильтрация цены
                    if($price_from && $price_to) {
                        $query .= " ((`p`.`price` >= {$price_from}) AND (`p`.`price` <= {$price_to}))";
                    }
                    elseif($price_from) {
                        $query .= " (`p`.`price` >= {$price_from})";
                    }
                    elseif($price_to) {
                        $query .= " (`p`.`price` <= {$price_to})";
                    }
                }

				$query .= ' AND ((`p`.`hidden` <> 1) OR ISNULL(`p`.`hidden`))';

                if ( $counter ) {
                    $query .= ' GROUP BY `p`.`id`, `a`.`id_attrs`';
                }
                else {
                    $query .= ' GROUP BY `p`.`id`';
                }
                
                $query .= ') AS `t` GROUP BY `id`';
                if ( $counter ) {
                    $query .= ' HAVING COUNT(`id`)='.$counter;
                }
                
                $productIDs = HDb::queryColumn($query);
                
                if ( empty($productIDs) ) {
                    return -1;
                }
                
                return $productIDs;
            }
        }

        return false;
    }

