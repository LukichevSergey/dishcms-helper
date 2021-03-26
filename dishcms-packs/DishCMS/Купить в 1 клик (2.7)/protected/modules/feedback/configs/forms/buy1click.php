<?php
/**
 * Купить в 1 клик
 */
return array(
    'buy1click' => array(
        'title' => 'Купить в 1 клик',
        'short_title' => 'Купить в 1 клик',
        // Options
        'options' => array(
            'useCaptcha' => false,
            'sendMail' => true,
            'emptyMessage' => 'Заказов нет',
        ),
        // Form attributes
        'attributes' => array(
            'name' => array(
                'label' => 'Имя',
                'type' => 'String', // String, Phone, Text, Checkbox, List
                'placeholder' => 'Ваше имя',
                'rules' => array(
                    array('name', 'required')
                ),
            ),
            'phone' => array(
                'label' => 'Контактный телефон',
                'type' => 'Phone',
                'rules' => array(
                    array('phone', 'required')
                ),
            ),
            'product_id' => array(
                'label' => 'ID товара',
                'type' => 'Hidden',
                'rules' => [['product_id', 'required']]
            ),
            'product_title' => array(
                'label' => 'Товар',
                'type' => 'Hidden',
            ),
            'privacy_policy' => array(
                'label' => 'Нажимая на кнопку "Отправить", я даю согласие на ' . \CHtml::link('обработку персональных данных', '/privacy-policy', ['target'=>'_blank']),
                'type' => 'Checkbox',
                'rules' => array(
                    array('privacy_policy', 'required')
                ),
                'htmlOptions'=>['class'=>'inpt inpt-privacy_policy', 'checked'=>'checked']
            ),
        ),
        // Control buttons
        'controls' => array(
            'send' => array(
                'title' => 'Оформить заказ'
            ),
        ),
    ),
);
