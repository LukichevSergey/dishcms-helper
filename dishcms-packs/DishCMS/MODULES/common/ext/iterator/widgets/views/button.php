<?php
/** @var \common\ext\iterator\widgets\Button $this */
use common\components\helpers\HArray as A;

$this->tag='div';
$this->tagOptions['style']=A::get($this->tagOptions, 'style', '') . ';position:relative;';

echo \CHtml::openTag($this->tag, $this->tagOptions);

    echo \CHtml::button($this->label, $this->htmlOptions);
    
    $this->progressOptions['class']=A::get($this->progressOptions, 'class', 'progress progress-striped active') . " {$this->getJsId()}-progress";
    $this->progressOptions['style']=A::get($this->progressOptions, 'style', 'display:none;width:95%;height:7px;bottom:-30px;position:absolute;');
    $this->progressBarOptions['class']=A::get($this->progressBarOptions, 'class', 'progress-bar') . " {$this->getJsId()}-progress-bar";
    echo \CHtml::tag('div', $this->progressOptions, \CHtml::tag('div', A::m([
        'role'=>'progressbar',
        'aria-valuenow'=>0,
        'aria-valuemin'=>0,
        'aria-valuemax'=>100,
        'style'=>'width:0%'
    ], $this->progressBarOptions)));
    
echo \CHtml::closeTag($this->tag);