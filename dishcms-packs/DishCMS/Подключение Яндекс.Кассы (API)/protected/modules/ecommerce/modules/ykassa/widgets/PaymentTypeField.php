<?php
namespace ykassa\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class PaymentTypeField extends \common\components\base\Widget
{
	/**
	 * @var array|boolean типы платежей.
	 * TRUE - все платежи. FALSE - ни один из платежей. Будет установлен 
	 * переданный в параметре $default.
	 */
	public $types=true;
	
	/**
	 * @var string|boolean тип платежа по умолчанию.
	 */
	public $default=false;
	
	/**
	 * @var string селектор кнопки submit.
	 */
	public $jSubmit='.payment-ym-button';
	
	public $view='payment_type_field';
	
	protected $assetsUrl;
	
	public function init()
	{
		parent::init();
		
		$this->assetsUrl=$this->publish();
		
		$options=['jSubmit'=>$this->jSubmit];
        
        Y::js(false, ';window.ykassa_widgets_PaymentTypeField.init('.\CJavaScript::encode($options).');', \CClientScript::POS_READY);
	}
	
	public function getIconSrc($icon)
	{
		return $this->assetsUrl . '/images/' . $icon;
	}
	
	public function getActiveTypes()
	{
		$types=[];
		
		if($this->types !== false) {
			foreach($this->getTypes() as $type=>$data) {
				if(($this->types === true) || in_array($type, $this->types) || isset($this->types[$type])) {
					if(array_key_exists($type, $this->types)) {
						$types[$type]=[];
						if(is_array($this->types[$type])) {
							foreach($this->types[$type] as $code) {
								if(isset($data['types'][$code])) {
									$types[$type]['types'][$code]=$data['types'][$code];
								}
							}
							$types[$type]['title']=A::get($data, 'title');
							$types[$type]['default']=A::get($data, 'default', false);
						}
						elseif(isset($data['types'][$this->types[$type]])) {
							$types[$type]['title']=A::get($data, 'title');
							$types[$type]['default']=A::get($data, 'default', false);
							$types[$type]['types'][$this->types[$type]]=$data['types'][$this->types[$type]];
						}
					}
					else {
						$types[$type]=$data;
					}
				}
			}
		}
		
		return $types;
	}
	
	public function getTypes()
	{
		$t=Y::ct('YkassaModule.widgets/paymentTypeField', 'ecommerce.ykassa');
		return [
			'phone'=>[
				'title'=>$t('type.phone.title'),
				'default'=>'MC',
				'types'=>[
					'beeline'=>[
						'code'=>'MC', 
						'icon'=>'phone/beeline.svg', 
						'title'=>$t('type.phone.beeline.title'),
					],
					'megafon'=>[
						'code'=>'MC', 
						'icon'=>'phone/megafon.svg', 
						'title'=>$t('type.phone.megafon.title'),
					],
					'mts'=>[
						'code'=>'MC', 
						'icon'=>'phone/mts.svg', 
						'title'=>$t('type.phone.mts.title'),
					],
					'tele2'=>[
						'code'=>'MC', 
						'icon'=>'phone/tele2.svg', 
						'title'=>$t('type.phone.tele2.title'),
					]
				]
			],
			'card'=>[
				'title'=>$t('type.card.title'),
				'default'=>'AC',
				'types'=>[
					'mir'=>[
						'code'=>'AC', 
						'icon'=>'card/mir.svg', 
						'title'=>$t('type.card.mir.title'),
					],
					'visa'=>[
						'code'=>'AC', 
						'icon'=>'card/visa.svg', 
						'title'=>$t('type.card.visa.title'),
					],
					'mastercard'=>[
						'code'=>'AC', 
						'icon'=>'card/mastercard.svg', 
						'title'=>$t('type.card.mastercard.title'),
					],
					'maestro'=>[
						'code'=>'AC', 
						'icon'=>'card/maestro.svg', 
						'title'=>$t('type.card.maestro.title'),
					],
					'applepay'=>[
						'code'=>'AC', 
						'icon'=>'card/applepay.svg', 
						'title'=>$t('type.card.applepay.title'),
					],
				]
			],
			'banking'=>[
				'title'=>$t('type.banking.title'),
				'default'=>false,
				'types'=>[
					'alfa'=>[
						'code'=>'AB', 
						'icon'=>'banking/alfa.svg', 
						'title'=>$t('type.banking.alfa.title'),
					],
					'erip'=>[
						'code'=>'EP', 
						'icon'=>'banking/erip.png', 
						'title'=>$t('type.banking.erip.title'),
					],
					'masterpass'=>[
						'code'=>'MA', 
						'icon'=>'banking/masterpass.svg', 
						'title'=>$t('type.banking.masterpass.title'),
					],
					'psb'=>[
						'code'=>'PB', 
						'icon'=>'banking/psb.svg', 
						'title'=>$t('type.banking.psb.title'),
					],
					'sberbank'=>[
						'code'=>'SB', 
						'icon'=>'banking/sberbank.svg', 
						'title'=>$t('type.banking.sberbank.title'),
					]
				]
			],
			'credit'=>[
				'title'=>$t('type.credit.title'),
				'default'=>'KV',
				'types'=>[
					'kupivkredit'=>[
						'code'=>'KV', 
						'icon'=>'credit/kupivkredit.png', 
						'title'=>$t('type.credit.kupivkredit.title'),
					]
				]
			],
			'emoney'=>[
				'title'=>$t('type.emoney.title'),
				'default'=>false,
				'types'=>[
					'qiwi'=>[
						'code'=>'QW', 
						'icon'=>'emoney/qiwi.svg', 
						'title'=>$t('type.emoney.qiwi.title'),
					],
					'webmoney'=>[
						'code'=>'WM', 
						'icon'=>'emoney/webmoney.svg', 
						'title'=>$t('type.emoney.webmoney.title'),
					],
					'yamoney'=>[
						'code'=>'PC', 
						'icon'=>'emoney/yamoney.svg', 
						'title'=>$t('type.emoney.yamoney.title'),
					],
				]
			],
		];
	}
}