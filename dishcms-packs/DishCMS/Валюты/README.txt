1) Добавить в Product.php
	use common\components\helpers\HCurrency;
	...
	public $price_origin=null;
    public $old_price_origin=null;
    const PRICE_ROUND_CEIL=1; // 0 - для отключения

    public function normalizePrice($price)
    {
    	return self::PRICE_ROUND_CEIL ? ceil($price) : round($price, 2, PHP_ROUND_HALF_UP);
    }

	/**
     * (non-PHPdoc)
     * @see \CActiveRecord::__get()
     */
    public function __get($name)
    {
    	if(($name == 'price') && ($this->price_origin !== null)) {
            return $this->price_origin;
        }
        elseif(($name == 'price') && ($this->currency == HCurrency::EUR)) {
            return $this->normalizePrice((float)parent::__get('price') * HCurrency::get(HCurrency::EUR, true));
        }
		elseif(($name == 'price') && ($this->currency == HCurrency::USD)) {
			return $this->normalizePrice((float)parent::__get('price') * HCurrency::get(HCurrency::USD, true));
        }
        elseif($name == 'price_rub') {
	        return $this->normalizePrice((float)parent::__get('price'));        
	    }
        elseif(($name == 'old_price') && ($this->old_price_origin !== null)) {
            return $this->old_price_origin;
        }
        elseif(($name == 'old_price') && ($this->old_price_currency == HCurrency::EUR)) {
	        return $this->normalizePrice((float)parent::__get('old_price') * HCurrency::get(HCurrency::EUR, true));
        }
		elseif(($name == 'old_price') && ($this->old_price_currency == HCurrency::USD)) {
			return $this->normalizePrice((float)parent::__get('old_price') * HCurrency::get(HCurrency::USD, true));
        }
        elseif($name == 'old_price_rub') {
            return $this->normalizePrice((float)parent::__get('old_price'));
        }

        return parent::__get($name);
    }

    /**
     * (non-PHPdoc)
     * @see \CActiveRecord::__set()
     */
    public function __set($name, $value)
    {
        if($name == 'price_origin') {
            return parent::__set('price', $value);
        }
        elseif($name == 'old_price_origin') {
            return parent::__set('old_price', $value);
        }

        return parent::__set($name, $value);
    }


    ...
    /**
     * Получить список валют
     * @return array
     */
    public function getCurrencies()
    {
        return [
            HCurrency::EUR=>HCurrency::EUR,
			HCurrency::USD=>HCurrency::USD
        ];
    }
    ...
    public function scopes()
    {
    		...
    		'cardColumns'=>['select'=>'`currency`, ...']
    		...
    }

	...
	public function rules()
	{
		...
		array('price, old_price, price_eur, price_usd', 'numerical', 'numberPattern'=>'/^[\d\s]+([.,][\d\s]+)?$/', 'message'=>'Число должно быть целым, либо в формате X.XX'),
		array('price_origin, old_price_origin, price, price_eur, price_usd, currency, old_price_currency, code, hidden, brand_id, description', 'safe')

	...

    public function attributeLabels()
    {
        return $this->getAttributeLabels(array(
		...
            'price' => 'Цена',
			'price_origin' => 'Цена',
			'price_eur' => 'Цена (EUR)',
			'price_usd' => 'Цена (USD)',
            'old_price' => 'Старая цена',
			'old_price_origin' => 'Старая цена',
			'old_price_currency'=>'Валюта',
			'currency'=>'Валюта',


Добавить в protected\config\dcart.php (для того, чтобы верно рачитывалась цена в корзине)
	'attributes' => [
		...
		'currency',

В шаблон protected\modules\ecommerce\modules\order\modules\admin\views\default\_order_detail.php
Добавить исключение 'currency'
	...
	if($data['value'] && !in_array($attribute, array('currency', ...


В файл шаблона добавить
protected\modules\admin\views\layouts\main.php

		<div class="usd-rub-panel">
                <span class="label label-info">EUR: <?= HCurrency::get(HCurrency::EUR); ?> руб</span><br/>
				<span class="label label-info">USD: <?= HCurrency::get(HCurrency::USD); ?> руб</span>
<? /*                <span class="label label-info">на <?= date('d.m.Y'); ?></span> */ ?>
         </div>


В шаблон формы
protected\modules\admin\views\shop\_form_product.php

<div class="row">
  	<div class="col-md-4" style="padding-left:0;">
	<?
	$model->price_origin=$model->price_rub;
	$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
        'attribute'=>'price_origin',
        'unit'=>$this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
            'attribute'=>'currency',
            'data'=>$model->getCurrencies(),
            'tag'=>false,
            'hideLabel'=>true,
            'hideError'=>true,
            'hideErrorTag'=>'span',
            'htmlOptions'=>['class'=>'form-control inline', 'empty'=>'РУБ', 'style'=>'margin-left:5px']
        ]), true),
        'unitOptions'=>['style'=>'display:inline-block'],
        'htmlOptions'=>['class'=>'w50 inline form-control']
	]));
		?>
    	</div>
    	<? if(D::cms('shop_enable_old_price')) { ?>
    	<div class="col-md-4">
            <?
            $model->old_price_origin=$model->old_price_rub;
            $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
                'attribute'=>'old_price_origin',
                'unit'=>$this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
                    'attribute'=>'old_price_currency',
                    'data'=>$model->getCurrencies(),
                    'tag'=>false,
                    'hideLabel'=>true,
                    'hideError'=>true,
                    'hideErrorTag'=>'span',
                    'htmlOptions'=>['class'=>'form-control inline', 'empty'=>'РУБ', 'style'=>'margin-left:5px']
                ]), true),
                'unitOptions'=>['style'=>'display:inline-block'],
                'htmlOptions'=>['class'=>'w50 inline form-control']
            ]));
            ?>
    	</div>
    	<? } ?>
    </div>


Добавить стили в protected\modules\admin\assets\css\style.less
.top_menu .usd-rub-panel {
    display: inline-block;
    margin-left: 10px;
    margin-top: 3px;
}
