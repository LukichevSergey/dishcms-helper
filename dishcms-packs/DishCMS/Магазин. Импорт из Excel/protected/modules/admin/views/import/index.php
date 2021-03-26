<?php
/**
 * @var $error string
 * @var $message string
 */
?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= $error ?>
    </div>
<?php endif; ?>

<?php if ($message): ?>
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= $message ?>
    </div>
<?php endif; ?>

<h1>Импорт Excel</h1>

<?php
$tabs = [
    'Импорт' => ['content'=>$this->renderPartial('_form_import', [], true), 'id'=>'tab-import'],
];

$this->widget('zii.widgets.jui.CJuiTabs', [
    'tabs'=> $tabs,
    'options'=>[]
]); ?>
