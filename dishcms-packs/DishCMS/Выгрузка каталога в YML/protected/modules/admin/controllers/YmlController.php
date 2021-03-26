<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 05.03.12
 * Time: 10:27
 * To change this template use File | Settings | File Templates.
 */
class YmlController extends AdminController
{
	public function actionDownload()
	{
		$this->file_force_download('yml/export.yml');
	}

	public function actionIndex()
	{
		$generator = Yii::app()->ymlGenerator;
		$generator->encoding = 'utf-8';

		if(Yii::app()->request->getPost('yml')){
			$categories = Yii::app()->request->getPost('categories');

			$notinclude = Yii::app()->request->getPost('notinclude');

			$categoriesNotIDs = $categoriesIDs = array();

			if($categories)
				foreach ($categories as $key => $value) {
					foreach(array_keys(Category::model()->findByPk($value)->descendants()->findAll(array('index'=>'id', 'select'=>'id'))) as $addKey) {
						$categoriesIDs[] = $addKey;
					}
					
					$categoriesIDs[] = $value;
				}

			if($notinclude)
				foreach ($notinclude as $id => $notincludeID)
					$categoriesNotIDs[] = $notincludeID;

			$generator->categoriesIDs = $categoriesIDs;

			$generator->categoriesNotIDs = $categoriesNotIDs;

			$generator->notexist = Yii::app()->request->getPost('notexist') ? true : false;

			$generator->run();

			$this->refresh();
		}

		$this->render('index');
	}

	protected function file_force_download($file) {
	  if (file_exists($file)) {
	    // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
	    // если этого не сделать файл будет читаться в память полностью!
	    if (ob_get_level()) {
	      ob_end_clean();
	    }
	    // заставляем браузер показать окно сохранения файла
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename=' . basename($file));
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($file));
	    // читаем файл и отправляем его пользователю
	    readfile($file);
	    exit;
	  }
	}

	// public function actionTest()
	// {
	// 	// $categories = Category::model()->findAll();
	// 	// foreach($categories as $category) {
	// 	// 	echo $category->id . ' ' . $category->getParent()->id . '<br>';
	// 	// }

	// 	//echo Yii::app()->createAbsoluteUrl('shop/product', array('id'=>102));

	// 	$product = Product::model()->findByPk(102);

	// 	echo Yii::app()->request->getBaseUrl(true) . $product->fullImg;
	// }
}
