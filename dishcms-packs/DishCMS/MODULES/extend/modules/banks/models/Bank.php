<?php
/**
 * Модель
 */
namespace extend\modules\banks\models;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class Bank extends \common\components\base\ActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'banks';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		$t=Y::ct('\extend\modules\banks\BanksModule.models/bank', 'banks');
		return A::m(parent::behaviors(), [
			'metaBehavior'=>['class'=>'\MetadataBehavior'],
			//'seoBehavior'=>['class'=>'\extend\modules\seo\behaviors\SeoBehavior'],
			'aliasBehavior'=>['class'=>'\DAliasBehavior'],
			'activeBehavior'=>[
				'class'=>'\common\ext\active\behaviors\ActiveBehavior',
				'attributeLabel'=>$t('label.active')
			],
			'logoBehavior'=>[
				'class'=>'\common\ext\file\behaviors\FileBehavior',
				'attribute'=>'logo',
				'attributeLabel'=>$t('label.logo'),
				'attributeEnable'=>'logo_enable',
				'attributeEnableLabel'=>$t('label.logo_enable'),
				'attributeAlt'=>'logo_alt',
				'attributeAltLabel'=>$t('label.logo_alt'),
    			'imageMode'=>true
			],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
		return $this->getScopes([
				
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return $this->getRelations([
			
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['title', 'required'],
			['preview_text, detail_text, bank_rate, down_payment, term_loan, decrease', 'safe']	
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		$t=Y::ct('\extend\modules\banks\BanksModule.models/bank', 'banks');
		return $this->getAttributeLabels([
			'title'=>$t('label.title'),
			'preview_text'=>$t('label.preview_text'),
			'detail_text'=>$t('label.detail_text'),
			'bank_rate'=>$t('label.bank_rate'),
			'down_payment'=>$t('label.down_payment'),
			'term_loan'=>$t('label.term_loan'),
			'decrease'=>$t('label.decrease')
		]);
	}
}