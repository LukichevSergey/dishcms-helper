1) В файл /protected/components/Controller.php добавить 
    public function init()
    {
        $theme = Yii::app()->user->getState('theme', 'adaptive_template_4');
        Yii::app()->theme = $theme;


2) В файле /protected/extensions/CmsHtml.php поправить
public static function less($files=array()) 
{
	if (!$files) {
    		if(Yii::app()->user->getState('theme', 'adaptive_template_4') == 'adaptive_template_4') {
	    		$files = array('client.less', 'template.less');
	    	}
	    	else {
    			$files = array('client.less', Yii::app()->user->getState('theme', 'template') . '.less');
    		}
    	}

3) В контроллер SiteController.php добавить действие
    public function actionChange()
    {
        $theme = Yii::app()->user->getState('theme', 'adaptive_template_4');

        Yii::app()->user->setState('theme', ($theme == 'adaptive_template_4') ? 'template_02' : 'adaptive_template_4');

        $this->redirect('/');
    }

4) Скопировать шаблон