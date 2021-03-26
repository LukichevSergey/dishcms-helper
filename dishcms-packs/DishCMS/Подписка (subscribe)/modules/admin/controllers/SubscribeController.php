<?php
Yii::setPathOfAlias('subscribe', Yii::getPathOfAlias('application.modules.subscribe'));
Yii::import('subscribe.SubscribeModule');

class SubscribeController extends \subscribe\controllers\AdminController
{	
}