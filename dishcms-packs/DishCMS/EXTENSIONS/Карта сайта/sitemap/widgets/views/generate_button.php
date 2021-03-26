<?php
/** @var \seo\ext\sitemap\widgets\SitemapGenerateButton $this */

use common\components\helpers\HArray as A;

$this->htmlOptions['encode']=false;
$this->htmlOptions['class']=trim(A::get($this->htmlOptions, 'class', '') . ' js-sitemap-generate-button-btn');
$this->htmlOptions['style']=A::get($this->htmlOptions, 'style', 'margin-right:5px;margin-bottom:12px;width:370px');
?>
<?php
$this->getOwner()->widget('\common\ext\iterator\widgets\Button', [
    'label'=>$this->label,
    'loadingText'=>$this->loadingText,
    'iterator'=>$this->iterator,
    'data'=>$this->getData(),
    'tag'=>$this->tag,
    'tagOptions'=>$this->tagOptions,
    'htmlOptions'=>$this->htmlOptions,
    'progressOptions'=>['style'=>'display:none;width:370px;height:7px;top:36px;position:absolute;'],
    'jsProcess'=>'try{$(".js-sitemap-generate-button-message").removeClass("alert-success alert-danger").addClass("alert-info").html(response.data.ipm.message).show();}catch{}',
    'jsError'=>'try{$(".js-sitemap-generate-button-message").removeClass("alert-success alert-info").addClass("alert-danger").html(`Произошла ошибка: ${response.errors.join("<br/>")}`).show();}catch{}',
    'jsDone'=>'try{$(".js-sitemap-generate-button-message").removeClass("alert-info alert-danger").addClass("alert-success").html(`'.$this->doneMessage.'`).show();}catch{}',
]);
?>
<div class="alert alert-info js-sitemap-generate-button-message" style="display:none;"></div>