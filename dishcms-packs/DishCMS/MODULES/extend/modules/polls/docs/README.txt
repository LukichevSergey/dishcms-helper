1) Добавить модуль в /modules/extend и подключить его в конфигурации /protected/modules/extend/config/main.php
 ...
 'modules'=>[
	'polls'=>['class'=>'\extend\modules\polls\PollsModule', 'autoload'=>true],


2) Подключить crud конфигурацию в /protected/config/crud.php
	...
	'extend.modules.polls.config.crud',

3) Добавить пункт меню в раздел администрирования /admin/config/menu.php
use crud\components\helpers\HCrud;

	'modules'=>array_merge([
		...
        HCrud::getMenuItems(Y::controller(), 'extend_polls', 'crud/index', true),

4) Пример использования в публичной части, где D::cms('quiz_id') добавляется 
отдельно, напр., для выбора основного голосования.

<?php
use extend\modules\polls\components\helpers\HPoll;
?>
<div class="main__container container">
    <?php $this->widget('\ext\D\breadcrumbs\widgets\Breadcrumbs', ['breadcrumbs' => $this->breadcrumbs->get()]); ?>
    <section class="quiz"><?php 
    if(D::cms('quiz_id')) {
        if(HPoll::isPassed(D::cms('quiz_id'))) {
            $this->widget('\extend\modules\polls\widgets\PollStat', ['id'=>D::cms('quiz_id')]);
        }
        else {
            $this->widget('\extend\modules\polls\widgets\Poll', ['id'=>D::cms('quiz_id')]);
        }
    }
    ?>
    </section>
</div>



