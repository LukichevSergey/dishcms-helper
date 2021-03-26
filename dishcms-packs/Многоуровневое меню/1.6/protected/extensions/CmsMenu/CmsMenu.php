<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 08.09.11
 * Time: 12:48
 * To change this template use File | Settings | File Templates.
 */
 
class CmsMenu
{
    private $_items = null;

    /**
     * @var CmsMenu
     */
    static $instance = null;
    static $countable = array('question');

    /**
     * Menu static instance
     * @static
     * @return CmsMenu
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function getItems($systemed=false, $forcibly=false, $root=false)
    {
        if ($forcibly || $this->_items == null) {
        	// @hook for menu module
        	$c = new CDbCriteria();
        	$c->order = 'ordering';
        	if($root) $c->addCondition('ISNULL(parent_id)');
        	if($systemed) $c->addCondition('system = 1');
            $this->_items = Menu::model()->findAll($c);
        }
        return $this->_items; 
    }

    private function getNextOrder()
    {
        $items = $this->getItems();
        $max   = $items[count($items)-1]->ordering;
        return $max < 0 ? 1 : $max + 1;
    }

    private function getItemNotify($modelName) {
        if($modelName == 'question') {
            return $count = Question::model()->unanswered()->count();
        }
    }

	public function adminMenu($systemed=false, $forcibly=false)
    {
        $items  = $this->getItems($systemed, ($forcibly || ($systemed == true)));    
        $result = array();

        foreach($items as $item) {
            $route = CmsMenuHelper::adminRoute($item);

            $itemOptions = array('id'=>'item-'. $item->id,
                                 'class' => '');

            if ($item->ordering < 0) {
                $itemOptions['class'] = 'ui-state-disabled';
            }
            if ($item->default) {
                $itemOptions['class'] = ' default';
            }

            if(isset($item->options['model']) && in_array($item->options['model'], self::$countable)){
                
                if($notify = $this->getItemNotify($item->options['model'])) 
                    $label = $item->title . "<span class='notify-wrap'><span class='notify'>".$notify."</span></span>";
                else
                    $label = $item->title;

                $result[] = array(
                    'label' => $label,
                    'url'   => $route,
                    'itemOptions'=>$itemOptions
                );

            } else {
                $result[] = array(
                    'label'=>$item->title,
                    'url'=>$route,
                    'itemOptions'=>$itemOptions
                );
            }
        }

        return $result;
    }

    public function siteMenu($root=false)
    {
        $items  = $this->getItems(false, true, $root);
        $result = array();

        $limit = Yii::app()->params['menu_limit'];

        foreach($items as $item) {
            if ($item->hidden) continue;

            $itemOptions = array();
            $linkOptions = array();

            $route = CmsMenuHelper::siteRoute($item, $itemOptions, $linkOptions);

            $result[] = array(
                'label' => $item->title,
                'url' => $route,
                'itemOptions' => $itemOptions,
                'linkOptions' => $linkOptions
            );
            
            if ($limit && count($result) == $limit) break;
        }

        return $result;
    } 

    /**
     * @param $model
     * @param null $item_id
     * @return bool | Menu
     */
    public function getItem($model, $item_id = null)
    {
        $items = $this->getItems();

        if (is_object($model)) {
            $item_id = $model->id;
            $model   = get_class($model);
        }

        if ($item_id == 'all') {
            $item_id = null;
        }

        foreach($items as $item) {
            if ($item_id) {
                if ($item->options['model'] == strtolower($model) &&
                    $item->options['id'] == $item_id)
                    return $item;
            } else {
                if ($item->options['model'] == strtolower($model))
                    return $item;
            }
        }

        return false;
    }

    public function getDefault()
    {
        foreach($this->getItems() as $item) {
            if ($item->default == 1)
                return $item;
        }
        return false;
    }

    public function addItem(& $model)
    {
        $options = array(
            'model' => strtolower(get_class($model)),
            'id' => $model->id
        );

        $item = new Menu;
        $item->title    = $model->title;
        $item->type     = 'model';
        $item->options  = $options;
        $item->ordering = $this->getNextOrder();

        if (!$item->save()) {
            throw new CHttpException('500', 'Ошибка создания пункта меню');
        }
        return $this;
    }

    public function updateItem(& $model)
    {
        $item = $this->getItem($model);

        if ($item) {
            $item->title = $model->title;
            $item->save();
        }
    }

    public function removeItem(& $model)
    {
        $item = $this->getItem($model);

        if ($item)
            $item->delete();

        return $this;
    }


    /**
     * Fix order of menu items
     * @param array $orders
     * @return void
     */
    public function reorder($orders = array())
    {
        $items = $this->getItems();

        $reset = !$orders || !is_array($orders) ? true : false;
        $i = 1;

        foreach($items as $item) {
            if ($item->ordering > 0) {
                $inx = !$reset ? array_search($item->id, $orders) : $i++ ;
                $item->ordering = $inx + 1;
                $item->save();
            }
        }
    }
}
