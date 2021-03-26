<?php
use common\components\helpers\HRequest as R;
use common\components\helpers\HAjax;

 	public function filters()
	{
		return \CMap::mergeArray(parent::filters(), array(
			array('DModuleFilter', 'name'=>'shop'),
			'ajaxOnly + add, clear, updateCount, getCount, inc, dec, counts'
		));
	}
	
	public function actionInc()
	{
		$ajax=HAjax::start();
		
		$id=(int)R::post('id');
		if($id) {
			if($model=Product::model()->findByPk($id)) {
				$cart=\Yii::app()->cart;
				if($hash=$cart->add($model, 1)) {
					$ajax->data['id']=$id;
					$ajax->data['count']=$cart->getCount($hash);
					$ajax->data['total']=$cart->getTotalCount();
					$ajax->success=true;
				}
			}
		}
			
		$ajax->end();
	}
	
	public function actionDec()
	{
		$ajax=HAjax::start();
		
		$id=(int)R::post('id');
		if($id) {
			$cart=\Yii::app()->cart;
			$data=$cart->getData(false, false, false);
			if(!empty($data)) {
				foreach($data as $hash=>$item) {
					if((int)$item['id'] == $id) {
						$count=$cart->getCount($hash) - 1;
						if($count < 1) {
							$cart->remove($hash);
							$count=0;
						}
						else {
							$cart->updateCount($hash, $count);
						}
						$ajax->data['id']=$id;
						$ajax->data['count']=$count;
						$ajax->data['total']=$cart->getTotalCount();
						$ajax->success=true;
						break;
					}
				}
			}
		}
			
		$ajax->end();
	}
	
	public function actionCounts()
	{
		$ajax=HAjax::start();
		
		$cart=\Yii::app()->cart;
		$data=$cart->getData(false, false, false);
		if(!empty($data)) {
			$items=[];
			foreach($data as $hash=>$item) {
				$items[]=[
					'id'=>$item['id'], 
					'count'=>$cart->getCount($hash)
				];
			}
			$ajax->data['items']=$items;
			$ajax->success=true;
		}
			
		$ajax->end();
	}
