<?php
	// FIX: удаление лишних файлов изображений созданных при клонировании товара
	public function actionFix2198()
    {
        die; // EXIT
        $imageFolder = $_SERVER['DOCUMENT_ROOT'] . '/images/';

        $images = [];

        $products = Product::model()->findAll();

        // get all product images
        foreach ($products as $product) {
            $images[] = $product->main_image;

            foreach ($product->moreImages as $image) {
                $images[] = $image->filename;
            }
        }

        // backup images
        /*foreach ($images as $image) {
            copy($imageFolder . 'product/' . $image, $imageFolder . 'product_backup/' . $image);
        }*/

        // remove images
        if ($handle = opendir($imageFolder . 'product/')) {

            while (false !== ($entry = readdir($handle))) {

                if ($entry != "." && $entry != "..") {

                    if (!in_array($entry, $images)) {
                        @unlink($imageFolder . 'product/' . $entry);
                    }
                }
            }

            closedir($handle);
        }
		
		echo 'done!';
    }
