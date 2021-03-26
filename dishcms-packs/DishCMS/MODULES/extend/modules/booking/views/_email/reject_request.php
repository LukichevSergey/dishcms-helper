<?php
use common\components\helpers\HHtml;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>

	<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
		<div style="width: 680px;">

			<p style="margin-top: 0px; margin-bottom: 20px;">
				<h1>Отмена бронирования на сайте <a href="http://<?php echo \Yii::app()->request->serverName; ?>" target="_blank"><?php \ModuleHelper::getParam('sitename'); ?></a></h1>
			</p>

			<? $first=true; foreach($requests as $request): if(!empty($ajax->data['messages'][$request->id])): ?>
				<?php if($first): $first=false; ?>
    				<?php foreach(['name'=>'Имя', 'phone'=>'Контактный телефон', 'comment'=>'Сообщение'] as $attribute=>$label): ?>
    				<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
    					<thead>
    						<tr>
    							<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?= $label; ?></td>
    						</tr>
    					</thead>
    					<tbody>
    						<tr>
    							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?= $request->$attribute; ?></td>
    						</tr>
    					</tbody>
    				</table>
    				<?php endforeach; ?>
				<?php endif; ?>
				<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
					<thead>
						<tr>
							<td colspan="4" style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">
								Номер заявки #<?= $request->id; ?>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
								<?= $request->getFormattedDate(); ?>
							</td>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
								<?= $request->count ?> чел.
							</td>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
								<?= HHtml::price($request->price) ?> руб/чел
							</td>
							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
								<?= HHtml::price((int)$request->count * (float)$request->price) ?> руб.
							</td>
						</tr>
					</tbody>
				</table>
			<? endif; endforeach; ?>
			
			<?php if($ajax->hasErrors): ?>
    			<h2>При оформлении бронирования возникли следующие ошибки</h2>
    			<? foreach($ajax->errors as $error): ?>
    				<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
    					<tbody>
    						<tr>
    							<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
    								<?= $error ?>
    							</td>
    						</tr>
    					</tbody>
    				</table>
    			<? endforeach; ?>
			<?php endif; ?>
		</div>
	</body>
</html>