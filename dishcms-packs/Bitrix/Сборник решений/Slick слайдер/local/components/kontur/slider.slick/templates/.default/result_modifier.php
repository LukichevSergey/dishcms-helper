<?php
use Bitrix\Main\Page\Asset;

$templatePath=dirname($this->GetFile());

if($arParams['PUBLISH_JS'] != 'N') {
     Asset::getInstance()->addJs($templatePath . '/assets/slick/slick.min.js');
}

if($arParams['PUBLISH_CSS'] != 'N') {
     Asset::getInstance()->addCss($templatePath . '/assets/slick/slick.css');
     Asset::getInstance()->addCss($templatePath . '/assets/slick/slick-theme.css');
}

if($arParams['PUBLISH_FANCYBOX_JS'] != 'N') {
     // Asset::getInstance()->addJs($templatePath . '/assets/fancybox/jquery.fancybox.min.js');
}

if($arParams['PUBLISH_FANCYBOX_CSS'] != 'N') {
     // Asset::getInstance()->addCss($templatePath . '/assets/fancybox/jquery.fancybox.min.css');
}
