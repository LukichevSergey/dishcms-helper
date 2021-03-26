<?php
/** @var \extend\modules\points\widgets\SearchForm $this */

if($this->tag) echo \CHtml::openTag($this->tag, $this->tagOptions);

echo \CHtml::beginForm('', 'post', $this->formOptions);
    echo \CHtml::textField('pointsearch', '', $this->getInputHtmlOptions());
    if($this->submitLabel) {
        echo \CHtml::htmlButton($this->submitLabel, $this->getSubmitHtmlOptions());
    }
    echo \CHtml::tag('ul', $this->getResultHtmlOptions(), '', true);
echo \CHtml::endForm();

if($this->gotoLabel) {
    echo \CHtml::htmlButton($this->gotoLabel, $this->getGotoHtmlOptions());
}

if($this->tag) echo \CHtml::closeTag($this->tag);
?>