<?php
// Оптимизация на TimeWeb (convert, jpegoptim, optipng)
// События которые срабатывают при создании или изменении элемента инфоблока
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "ResizeUploadedPhoto");
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "ResizeUploadedPhoto");

function ResizeUploadedPhoto(&$arFields) {
	global $APPLICATION;
  	CModule::IncludeModule('iblock');
	$imageMaxWidth = 1280; // Максимальная ширина картинки
	$imageMaxHeight = 1280; // Максимальная высота картинки
	$files=array();
	if(isset($arFields['PREVIEW_PICTURE']['tmp_name']) && is_file($arFields['PREVIEW_PICTURE']['tmp_name'])) {
		$files[]=$arFields['PREVIEW_PICTURE']['tmp_name'];
	}
	if(isset($arFields['DETAIL_PICTURE']['tmp_name']) && is_file($arFields['DETAIL_PICTURE']['tmp_name'])) {
		$files[]=$arFields['DETAIL_PICTURE']['tmp_name'];
	}
	foreach(array('PREVIEW_PICTURE_ID', 'DETAIL_PICTURE_ID') as $propName) {
		if(isset($arFields[$propName])) {
			if($fileArray=CFile::GetFileArray($arFields[$propName])) {
				$filename=$_SERVER["DOCUMENT_ROOT"].$fileArray['SRC'];
				if(is_file($filename)) $files[md5($filename)]=$filename;
			}
		}
	}
	
	$propFiles=array();
	if(isset($arFields['PROPERTY_VALUES']) && is_array($arFields['PROPERTY_VALUES'])) {
		foreach($arFields['PROPERTY_VALUES'] as $propId=>$properties) {
			if(is_array($properties)) {
				foreach($properties as $property) {
					if(isset($property['VALUE']['tmp_name'])) {
						$files[]=$property['VALUE']['tmp_name'];
						if(!isset($propFiles[$propId])) {
							$propFiles[$propId]=$propId;
						}
					}
				}
			}
		}
	}
	
	if(!empty($propFiles)) {
		foreach($propFiles as $propId) {
			$res=CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], "sort", "asc", array("ID"=>$propId));
			while ($ob=$res->GetNext()) {
				$filename=$_SERVER["DOCUMENT_ROOT"].CFile::GetPath($ob['VALUE']);
				if(is_file($filename)) {
					$files[md5($filename)]=$filename;
				}
			}
		}
	}
	
	if(!empty($files)) {
		$jpgs=array();
		$pngs=array();
		$resizes=array();
		foreach($files as $filename) {
			$type=exif_imagetype($filename);
			if(($type == IMAGETYPE_JPEG) || ($type == IMAGETYPE_PNG)) {
				if($type==IMAGETYPE_JPEG) $jpgs[]=$filename;
				else $pngs[]=$filename;
				
				$ratioW=0;
				$ratioH=0;
				$size=getimagesize($filename);
				if($size[0] > $imageMaxWidth) {
					$ratioW=floor(100 / ($size[0]/$imageMaxWidth));
				}
				if($size[1] > $imageMaxHeight) {
					$ratioH=floor(100 / ($size[1]/$imageMaxHeight));
				}
				
				if($ratioW || $ratioH) {
					$resizes[md5($filename)]=array($filename, (($ratioW && ($ratioW < $ratioH) || !$ratioH) ? $ratioW : $ratioH));
				}
			}
		}	
	}
	
	if(!empty($resizes)) {
		foreach($resizes as $params) {
			exec('convert '.$params[0].' -resize '.$params[1].'% '.$params[0], $output);
		}
	}
	if(!empty($jpgs)) {
		foreach($jpgs as $filename) {
			exec('jpegoptim --strip-all --all-progressive -pm85 '.$filename, $output);
		}
	}
	if(!empty($pngs)) {
		foreach($pngs as $filename) {
			exec('optipng '.$filename, $output);
		}
	}
}
