<?php
/** @var \extend\modules\booking\controllers\DefaultController $this */
/** @var [] $schedule */
/** @var \DateTimeImmutable $date */
use common\components\helpers\HYii as Y;
use common\components\helpers\HHash;
use common\components\helpers\HTools;
use extend\modules\booking\components\helpers\HBooking;

$jsId=HHash::ujs();
?>
<div id="reserve-form" class="reserve-form-<?=$jsId?>">
    <div class="popup-info">
    	<div class="reserve-form-alert js-reserve-alert" style="display:none">
    		<div class="reserve-form-alert-header js-reserve-alert-header">
    			Бронирование на <?= $date->format('d'); ?> <?= HBooking::getShortMonthLabel($date->format('m'))?>, <?= $date->format('Y'); ?>
    		</div>
    		<div class="reserve-form-alert-errors">
    			<div class="reserve-form-alert-errors-header">При бронировании возникли следующие ошибки:</div>
    			<div class="reserve-form-alert-errors-text js-reserve-alert-errors"></div>
    		</div>
			<div class="reserve-form-alert-info-header js-reserve-alert-info-header">Поздравляем, <span class="js-reserve-alert-info-header-name"></span>!</div>
    		<div class="reserve-form-alert-info">
    			<div class="reserve-form-alert-info-text js-reserve-alert-info"></div>
    		</div>
    		<div class="reserve-form-alert-reject js-reserve-alert-reject">
    			<a href="javascript:;" class="js-reserve-alert-reject-btn">отменить бронирование</a>
    		</div>
    	</div>
        <form class="reserve-form js-reserve-form" class="" action="" method="post">
          <div class="reserve-form-title-date"><?= $date->format('d'); ?> <?= HBooking::getShortMonthLabel($date->format('m'))?>, <?= $date->format('Y'); ?></div>
          <div class="reserve-form-title">
          	Выберите удобное для вас время!
          </div>
          <div class="reserve-form-time">
            <div class="row">
          	  <?php $idx=1; foreach($schedule as $hash=>$day): $reserved=!$day['free_count']; ?>
              <div class="col-lg-3 col-md-4 col-6">
              	<div class="reserve-form-time-item<? if($reserved) echo ' reserved'; ?>">
              		<?php if(!$reserved): ?>
                      <?= \CHtml::tag('input', [
                          'class'=>'js-reserve-checkbox',
                          'id'=>'reserve-checkbox-' . $idx,
                          'type'=>'checkbox',
                          'name'=>'time',
                          'value'=>$date->format('d.m.Y'),
                          'data-from'=>HBooking::formatTime($day['from']['h'], $day['from']['i']),
                          'data-to'=>HBooking::formatTime($day['to']['h'], $day['to']['i']),
                          'data-price'=>$day['price'],
                          'data-free'=>$day['free_count'],
                          'data-hash'=>$day['schedule_hash'],
                      ]); ?>
                    <?php endif; ?>
                  <label for="reserve-checkbox-<?=$idx?>"><span class="reserve-checkbox-inner">
                    <span><?=HBooking::formatTime($day['from']['h'], $day['from']['i'])?> - <?=HBooking::formatTime($day['to']['h'], $day['to']['i'])?></span>
                    <div class="reserve-form-time-places">
                    	<?php if($reserved): ?>
                    		(забронировано)
                    	<?php else: ?>
                    		(<span><?= $day['free_count']; ?></span> <?=HTools::pluralLabel($day['free_count'], ['место', 'места', 'мест'])?>)
                    	<?php endif; ?>
                    </div>
                  </span>
                </label>
                </div>
              </div>
              <?php $idx++; endforeach; ?>
            </div>
          </div>

          <div class="reserve-form-count">
            <div class="reserve-form-count-left">
              <span>Количество человек</span>
              <div class="reserve-form-count-set">
                <div class="reserve-form-count-minus js-reserve-count-minus">-</div>
                <div class="reserve-form-count-value js-reserve-count">0</div>
                <div class="reserve-form-count-plus js-reserve-count-plus">+</div>
              </div>
            </div>

            <div class="reserve-form-count-right">
              <div class="reserve-form-price">
                <span class="js-reserve-price">0</span> руб
              </div>
            </div>
          </div>

          <div class="reserve-form-text">
            <div class="row">
              <div class="col-md-6">
                <input type="text" placeholder="Ваше имя" name="name" value="" class="js-reserve-name">
                <input type="text" placeholder="Ваш номер телефона" name="phone" value="" class="js-reserve-phone">
              </div>

              <div class="col-md-6">
                <textarea placeholder="Сообщение" name="comment" class="js-reserve-comment"></textarea>
              </div>
            </div>
          </div>

          <div class="reserve-form-footer">
            <div class="reserve-form-privacy">
              <input type="checkbox" name="privacy" value="" id="reserve-form-privacy-checkbox" class="js-reserve-privacy">
              <label for="reserve-form-privacy-checkbox"><span>Я принимаю условия <a href="/privacy-policy">пользовательского соглашения</a></span></label>
            </div>
            <div class="reserve-form-submit">
              <button type="button" class="btn js-reserve-submit">Забронировать</button>
            </div>
          </div>
        </form>
    </div>
</div>
<script>$(function(){
	function s(name){return '.reserve-form-<?=$jsId?> .js-reserve-' + name;}
	function j(name){return $(s(name));}
	function max_count(){let max=0;j('checkbox:checked').each(function(){let free=+$(this).data('free');if(!isNaN(free) && (!max || (max > free))){max=free;}});return max;}
	function update_count(e){let c=+j('count').text();
	if(isNaN(c)){c=1;}if((typeof e.data.n == "undefined")||isNaN(+e.data.n)){e.data.n=0;}c+=e.data.n;if(c < 1)c=1;
	if(c>max_count()){c=max_count();}j('count').text(c);calc();e.stopImmediatePropagation();}
	$(document).on("click", s('count-minus'), {n:-1}, update_count);
	$(document).on("click", s('count-plus'), {n:1}, update_count);
	Number.prototype.format = function(n, x, s, c) {
	    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',num = this.toFixed(Math.max(0, ~~n));
	    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ' '));
	};
	function calc() {
		let total=0;j('checkbox').filter(':checked').each(function(){
			let price=+$(this).data('price');if(isNaN(price)||(price < 0)){price=0;}
			total+=price;
		});
		total=total * +j('count').text();
		j('price').text(total.format(0));
	}
	$(document).on('change', s('checkbox'), function(e){
		let max=max_count(),c=+j('count').text();
		if(!c&&(max>0)){j('count').text(1);}
		else if(c>max){j('count').text(max);}
		calc();
	});
	$(document).on('click', s('alert-reject-btn'), function(e) {
		if(confirm('Отменить заявку на бронирование?')) {
    		$.post('/extend/booking/default/reject?<?=time()?>', {reject: $(e.target).attr('data-reject')}, function(r) {
    			j('alert-errors').parent().hide();
    			j('alert-info-header').hide();
    			j('alert-info').parent().hide();
    			j('alert-reject').hide();
    			if(r.hasErrors) {
    				j('alert-errors').html(r.errors.join('<br/>'));
    				j('alert-errors').parent().show();
    			}
    			if(r.success) {
    				let msg=[];for(let i in r.data.messages){msg.push(r.data.messages[i]);}
    				j('alert-info').html(msg.join('<br/>'));
    				j('alert-info').parent().show();
    			}
    			j('form').hide();
    			j('alert').show();
    			
    		}, 'json');
		}
	});
	$(document).on('click', s('submit'), function(e) {
		let hasErrors=false;
		if(!j('name').val().length){hasErrors=true;j('name').addClass('error');}else{j('name').removeClass('error');} 
		if(!j('phone').val().length){hasErrors=true;j('phone').addClass('error');}else{j('phone').removeClass('error');} 
		if(!j('privacy').is(':checked')){hasErrors=true;j('privacy').parent().addClass('error');}else{j('privacy').parent().removeClass('error');}
		if(!j('checkbox:checked').length){hasErrors=true;j('checkbox').parent().addClass('error');}else{j('checkbox').parent().removeClass('error');}
		if(!hasErrors){
			let data={},items=[];
			j('checkbox:checked').each(function(){
				let item={};
				item.date=$(this).val();
				item.hash=$(this).data('hash');
				item.time=$(this).data('from');
				items.push(item);
			});
			data.items=items;
			data.name=j('name').val();
			data.phone=j('phone').val();
			data.comment=j('comment').val();
			data.count=j('count').text();
			$.post('/extend/booking/default/booking?<?=time()?>', data, function(r) {
				j('alert-errors').parent().hide();
				j('alert-info-header').hide();
				j('alert-info').parent().hide();
				j('alert-reject').hide();
				if(r.hasErrors) {
					j('alert-errors').html(r.errors.join('<br/>'));
					j('alert-errors').parent().show();
				}
				if(r.success) {
					j('alert-info-header-name').text(r.data.name);
					let msg=[];for(let i in r.data.messages){msg.push(r.data.messages[i]);}
					j('alert-info').html(msg.join('<br/>'));
					j('alert-info-header').show();
					j('alert-info').parent().show();
					j('alert-reject-btn').attr('data-reject', r.data.reject);
					j('alert-reject').show();
				}
				j('form').hide();
				j('alert').show();
			}, 'json'); 
		} 
	});
	$(s('phone')).mask('+7 (999) 999 - 99 - 99');
});</script>