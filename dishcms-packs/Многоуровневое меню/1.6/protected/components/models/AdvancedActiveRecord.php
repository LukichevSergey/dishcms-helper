<?php
/**
 * Advanced ActiveRecord
 * 
 */
class AdvancedActiveRecord extends CActiveRecord
{
	/**
	 * Get Data Provider
	 *  
	 * @param CDbCriteria|null $criteria
	 * @param CPagination|null $pagination
	 * @return CActiveDataProvider
	 */
    public function getDataProvider($criteria=null, $pagination=null) 
    {
    	if(!($criteria instanceof CDbCriteria)) $criteria = new CDbCriteria();
    	if(!($pagination instanceof CPagination)) {
    		$pagination = new CPagination();
    		$pagination->pageVar = 'p';
    		$pagination->pageSize = 15; 
    	}
    	
    	return new CActiveDataProvider($this, array(
        	'criteria'=> $criteria,
            'pagination' => $pagination
        ));
    }
    
    /**
     * Use it, if you wont use "scopes" and "cActiveDataProvider".
     * @author Rafael Garcia
     * @link http://www.yiiframework.com/wiki/173/an-easy-way-to-use-escopes-and-cactivedataprovider/
     *
     * @ Param Criteria $ CDbCriteria
     * @ Return CActiveDataProvider
     */
    public function getScopesDataProvider($criteria=null, $pagination=null)
    {
        if ((is_array ($criteria)) || ($criteria instanceof CDbCriteria) )
            $this->getDbCriteria()->mergeWith($criteria);
        $pagination = CMap::mergeArray (array('pageSize' => 2), (array)$pagination);
        return new CActiveDataProvider($this, array(
                'criteria'=>$this->getDbCriteria(),
                'pagination' => $pagination
        ));
    }
    
    /**
     * Active Captcha validator
     * 
     * Use $params['formId'] - Active form id.
     * 
     * @author andy_s, Gloss82
     * @link http://www.yiiframework.com/forum/index.php/topic/11063-%D0%BD%D0%B5-%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%B0%D0%B5%D1%82-ajax-%D0%B2%D0%B0%D0%BB%D0%B8%D0%B4%D0%B0%D1%86%D0%B8%D1%8F-%D1%84%D0%BE%D1%80%D0%BC/ 
     */
    public function activeCaptcha($attribute, $params)
    {
    	$code = Yii::app()->controller->createAction('captcha')->getVerifyCode();
    	if ($code != $this->verifyCode)
    		$this->addError('verifyCode', 'Неправильный код проверки.');
    	if (!(isset($_POST['ajax']) && isset($params['formId']) && $_POST['ajax']===$params['formId']))
    		Yii::app()->controller->createAction('captcha')->getVerifyCode(true);
    }
}