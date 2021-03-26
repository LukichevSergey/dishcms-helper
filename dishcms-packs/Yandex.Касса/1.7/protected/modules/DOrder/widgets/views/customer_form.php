<?php
/** @var \DOrder\widgets\CustomerFormWidget $this */
?>
<div class="form" style="width: 60%">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'dorder-customer-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ),
    )); /* @var CActiveForm $form */ ?>

    <div class="row">
        <?php echo $form->labelEx($this->model, 'name'); ?>
        <?php echo $form->textField($this->model, 'name'); ?>
        <?php echo $form->error($this->model, 'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($this->model, 'email'); ?>
        <?php echo $form->textField($this->model, 'email'); ?>
        <?php echo $form->error($this->model, 'email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($this->model, 'phone'); ?>
        <?php
	        $this->widget('CMaskedTextField', array(
	            'model' => $this->model,
	            'attribute' => 'phone',
	            'mask' => '+7 ( 999 ) 999 - 99 - 99',
				'htmlOptions' => array('placeholder'=>'+7 ( ___ ) ___ - __ - __')
	        ));
	    ?>
        <?php echo $form->error($this->model, 'phone'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($this->model, 'address'); ?>
        <?php echo $form->textArea($this->model, 'address'); ?>
        <?php echo $form->error($this->model, 'address'); ?>
    </div>

    <?php if ($this->model->scenario == 'payment'): ?>
    <div class="row inline">
        <?php echo $form->labelEx($this->model, 'payment'); ?>
        <?php echo $form->radioButtonList($this->model, 'payment', $this->model->getPaymentTypes(), array('class'=>'inline', 'labelOptions'=>array('class'=>'inline'))); ?>
        <?php echo $form->error($this->model, 'payment'); ?>
    </div>
    <?php endif; ?>

    <div class="row">
        <?php echo $form->labelEx($this->model, 'comment'); ?>
        <?php echo $form->textArea($this->model, 'comment'); ?>
        <?php echo $form->error($this->model, 'comment'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($this->submitTitle); ?>
    </div>

    <?php $this->endWidget(); ?>
</div>


<form name=ShopForm method="POST" action="https://money.yandex.ru/eshop.xml">
<font face=tahoma size=2>
 
<input type="hidden" name="scid" value="23207">
<input type="hidden" name="ShopID" value="32699"> 
 
Идентификатор клиента/Номер заказа:<br> 
<input type=text name="CustomerNumber" size="43"><br><br> 
Сумма:<br> 
<input type=text name="Sum" size="43"><br><br>   
Ф.И.О.:<br> 
<input type=text name="CustName" size="43"><br><br>
Адрес доставки:<br> 
<input type=text name="CustAddr" size="43"> <br><br> 
E-mail:<br> 
<input type=text name="CustEMail" size="43"><br><br>  
Содержание заказа:<br> 
<textarea rows="10" name="OrderDetails" cols="34"></textarea><br><br> 
Способ оплаты:<br><br>
<input name="paymentType" value="PC" type="radio" checked="checked"/>Со счета в Яндекс.Деньгах<br/>
<input name="paymentType" value="AC" type="radio" />С банковской карты<br/>
<input type=submit value="Оплатить"><br> 
</form> 
 