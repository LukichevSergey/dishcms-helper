<?php
namespace extend\modules\forms\behaviors;

use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;
use crud\models\ar\extend\modules\forms\models\Config;

class ConfigCrudControllerBehavior extends \CBehavior
{
    /**
     * Сохранение конфигурации формы
     * @param string $cid идентификатор конфигурации CRUD форм
     */
    public function actionSave($cid)
    {
        $id=R::get('id');
        
        if(!empty($id) && $config=Config::model()->findByPk($id)) {
            $attributes=$config->getAttributes();
            $content=json_encode($config->getAttributes(), JSON_UNESCAPED_UNICODE);
            HFile::download('form_'.$config->code.'.json', $content);
        }
        
        R::e404();
    }
}