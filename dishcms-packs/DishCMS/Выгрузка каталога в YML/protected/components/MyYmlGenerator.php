<?php

/**
 * Sample Yandex.Market Yml generator
 */

class MyYmlGenerator extends YmlGenerator {
    
    protected function shopInfo() {
        return array(
            'name'=>'',
            'company'=>'',
            'url'=>Yii::app()->request->getBaseUrl(true),
            // 'platform'=>'Dishman CMS',
            // 'version'=>'1.7.1.6',
            // 'agency'=>'ООО "КОНТУР"',
            // 'email'=>'n.aredakova@kontur-nsk.ru'
      );
    }
    
    protected function currencies() {
        $currencies = array(
            array('id'=>'RUR', 'rate'=>'1'),
            array('id'=>'USD', 'rate'=>'CBRF'),
            array('id'=>'EUR', 'rate'=>'CBRF'),
            array('id'=>'UAH', 'rate'=>'CBRF'),
        );

        foreach($currencies as $currecy) {
            $this->addCurrency($currecy['id'],$currecy['rate']);
        }
    }
    
    protected function categories() {
        $criteria = new CDbCriteria;

        if($this->categoriesIDs)
            $criteria->addInCondition('id', $this->categoriesIDs);

        if($this->categoriesNotIDs)
            $criteria->addNotInCondition('id', $this->categoriesNotIDs);

        $categories = Category::model()->findAll($criteria);

        foreach($categories as $category) {
            $this->addCategory($category->title,$category->id,$category->getParent()->id);
        } 

    }
    
    protected function offers() {
        $criteria = new CDbCriteria;

        if($this->categoriesIDs)
            $criteria->addInCondition('category_id', $this->categoriesIDs);

        if($this->categoriesNotIDs)
            $criteria->addNotInCondition('category_id', $this->categoriesNotIDs);

        if($this->notexist)
            $criteria->addCondition('notexist = 0');

        $criteria->addCondition('price > 0');

        $offers = Product::model()->findAll($criteria);

        $categories=Category::model()->findAll(['index'=>'id', 'select'=>'id, title']);

        foreach($offers as $offer) {
            $id = $offer->id;

            $description = $offer->description;
            $description = str_replace('<br>', '<br/>', $description);
            // $description = str_replace('</p>', "</p>\r\n", $description);
            $description = strip_tags($description, '<p><br><h3><ul><li><br/>');

            $data = array(
            	// 'type' => 'vendor.model',
            	// 'vendor'=>'',
            	// 'typePrefix'=>$categories[$offer->category_id]->title,
                'url' => Yii::app()->createAbsoluteUrl('shop/product', array('id'=>$id)),
                'price' => $offer->price,
                'currencyId' => 'RUR',
                'categoryId' => $offer->category_id,
                'picture' => Yii::app()->request->getBaseUrl(true) . $offer->getSrc(),
                'description' => CHtml::cdata($description),
                'name' => $offer->title,
            );

            // if($offer->old_price) {
            //	 $data['oldprice']=$offer->old_price;
            // }

            if($offer->code) 
                $data['vendorCode'] = $offer->code;

            $this->addOffer($id, $data, [], !$offer->notexist, false);
            // $this->addOffer($id, $data, [], !$offer->notexist);
        }
    }
}

