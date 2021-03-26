<?php
/** @var \common\ext\parser\widgets\ParserCommandIndicator $this */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\ext\parser\models\Config;

$configHashVar=Config::CONFIG_HASH_VAR;
$jsAjaxUpdateCode=<<<EOT
function {$this->getJsId()}_update() {
\$.post("/common/crud/admin/default/ajax",{cid:"common_ext_parser_parser", action:"getCommandProcessPercent", is_periodic:{$this->isPeriodic}, {$configHashVar}:"{$this->getConfigHash()}"},function(r){
    let percent=(!r.success || isNaN(+r.data.percent)) ? 0 : +r.data.percent;
    let bar=\$(".{$this->getJsId()}-progress-bar"),progress=\$(".{$this->getJsId()}-progress"),txt=\$(".{$this->getJsId()}-progress-text");
    bar.attr("aria-valuenow", percent);bar.width(percent + "%");txt.text(percent + "%");
    if(percent >= 100) { progress.removeClass("progress-striped").removeClass("active");bar.addClass("progress-bar-success"); }
    else if(bar.hasClass("progress-bar-success")) { bar.removeClass("progress-bar-success");progress.addClass("progress-striped").addClass("active"); }
},"json");};setInterval({$this->getJsId()}_update,{$this->delay});{$this->getJsId()}_update();
EOT;
Y::js(false, $jsAjaxUpdateCode, \CClientScript::POS_READY);

$this->tag='div';
$this->tagOptions['style']=A::get($this->tagOptions, 'style', '') . ';position:relative;';

echo \CHtml::openTag($this->tag, $this->tagOptions);

if($this->label) {
    echo \CHtml::label($this->label, '', $this->labelOptions);
}

$this->progressOptions['class']=A::get($this->progressOptions, 'class', 'progress progress-striped active') . " {$this->getJsId()}-progress";
$this->progressOptions['style']=A::get($this->progressOptions, 'style', 'width:95%;height:16px;top:-6px;position:relative;');
$this->progressBarOptions['class']=A::get($this->progressBarOptions, 'class', 'progress-bar') . " {$this->getJsId()}-progress-bar";
if($this->textTag) {
    $this->textOptions['class']=A::get($this->textOptions, 'class', '') . " {$this->getJsId()}-progress-text";
    $this->textOptions['style']=A::get($this->textOptions, 'style', 'font-size:0.9em;position:relative;top:-2px;');
    $text=\CHtml::tag($this->textTag, $this->textOptions, '0%');
}
echo \CHtml::tag('div', $this->progressOptions, \CHtml::tag('div', A::m([
    'role'=>'progressbar',
    'aria-valuenow'=>0,
    'aria-valuemin'=>0,
    'aria-valuemax'=>100,
    'style'=>'width:0%'
], $this->progressBarOptions), (empty($text) ? '' : $text)));

if($this->note) {
    if($this->noteTag) echo \CHtml::openTag($this->noteTag, $this->noteOptions);
    echo $this->note;
    if($this->noteTag) echo \CHtml::closeTag($this->noteTag);
}

echo \CHtml::closeTag($this->tag);