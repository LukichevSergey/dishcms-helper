<?php
Yii::setPathOfAlias('pages', Yii::getPathOfAlias('application.modules.pages'));
Yii::import('pages.PagesModule');

class PagesController extends \pages\modules\admin\controllers\DefaultController
{	
}