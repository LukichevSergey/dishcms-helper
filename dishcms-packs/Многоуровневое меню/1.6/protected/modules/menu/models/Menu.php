<?php
/**
 * This is the model class for table "menu".
*
* The followings are the available columns in table 'menu':
* @property integer $id
* @property integer $parent_id
* @property string $title
* @property string $type
* @property string $options
* @property integer $ordering
* @property bool $default
* @property bool $hidden
* @property bool $system Является ли пункт меню системным?
*/
namespace menu\models;

use \AttributeHelper as A;

class Menu extends \AdvancedActiveRecord
{
	/**
	 * @static
	 * @param string $className
	 * @return CActiveRecord
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'menu';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title', 'required'),
			array('ordering', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('parent_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, ordering', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
		return array(
			'ordering' => array('order'=>'ordering'),
			'visibled'=> array('condition'=>'hidden = 0'),
			'system' => array('condition'=>'system = 1'),
			'nonsystem' => array('condition'=>'system <> 1')
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'ordering' => 'Ordering',
		);
	}

	/**
	 * Инсталляция для Базы Данных
	 * @param string $integrateMode флаг режима интеграции с меню старого Dishman'a.
	 */
	public function install($integrateMode=false)
	{
		if($integrateMode) {
			$command = \Yii::app()->db->createCommand();
			if(!isset($this->tableSchema->columns['parent_id'])) { 
				$command->addColumn($this->tableName(), 'parent_id', "INT(11) COMMENT 'Parent id'");
			}
			if(!isset($this->tableSchema->columns['parent_id'])) {
				$command->addColumn($this->tableName(), 'system', "TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Is system'");
			}
		}
		else {
			$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->tableName() . ' (
	  			`id` INT(11) NOT NULL AUTO_INCREMENT COMMENT \'Id\',
				`parent_id` INT(11) COMMENT \'Parent id\',
	  			`title` VARCHAR(255) NOT NULL COMMENT \'Title\',
	  			`type` VARCHAR(255) NOT NULL DEFAULT \'model\' COMMENT \'Type\',
	  			`options` VARCHAR(255) NOT NULL DEFAULT \'\' COMMENT \'Options\',
	  			`ordering` INT(11) NOT NULL DEFAULT 1 COMMENT \'Ordering\',
	  			`default` TINYINT(1) NOT NULL DEFAULT 0 COMMENT \'Default\',
	  			`hidden` TINYINT(1) NOT NULL DEFAULT 0 COMMENT \'Is hidden\',
				`system` TINYINT(1) NOT NULL DEFAULT 0 COMMENT \'Is system\',
	  			PRIMARY KEY (`id`),
	  			UNIQUE INDEX `id` (`id`)
			) ENGINE = INNODB CHARACTER SET utf8;';
			
			\Yii::app()->db->createCommand($sql)->execute();
		}
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('ordering',$this->ordering);

		return new CActiveDataProvider(get_class($this), array(
				'criteria'=>$criteria,
		));
	}

	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::afterFind()
	 */
	public function afterFind()
	{
		$options = json_decode($this->options);
		if (is_object($options))
			$options = (array) $options;
		$this->options = $options;
	}

	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
		if (is_array($this->options)) {
			$this->options = json_encode($this->options);
		}
		return true;
	}
	
	/**
	 * Update tree
	 * @param array $items menu items where each item like as
	 * array(
	 * 	'id'=><pk>,
	 * 	'visbile'=><visible(true)/hidden(false) value> (данный параметр не обязательный)
	 * 	'childs'=><children items array like as items array>)
	 * @param integer|null $parentId items parent id. Default (null) is root.
	 * @return boolean is updated? 
	 */
	public function updateTree($items, $parentId=null)
	{
		$result = false;
		
		$transaction = \Yii::app()->db->beginTransaction();
		try {
			$model = self::model();
			// @var function update menu items. 
			// @param array $items menu items where each item like as
			// array('id'=><pk>, 'childs'=><children items array like as items array>)
			// @param int $parentId items parent id.
			$funcUpdate = function ($items, $parentId=null) use (&$funcUpdate, &$model) {
				foreach($items as $order=>$item) {
					if($id = A::get($item, 'id')) {
						$attributes = array(
							'parent_id' => $parentId ?: null,
							'ordering' => ($order + 1)
						);
						if(($visible = A::get($item, 'visible')) !== null) {
							$attributes['hidden'] = (int)$visible ? 0 : 1;
						}
						$model->updateByPk($id, $attributes);
						
						$childs = A::get($item, 'childs', array());
						$funcUpdate($childs, $id);
					}
				}
			};
			$funcUpdate($items);
			$transaction->commit();
			
			$result = true;
		}
		catch(\Exception $e) {
			$transaction->rollback();
		}
		
		return $result;
	}
}
