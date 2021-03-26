<?php
/** @var \extend\modules\booking\widgets\Calendar $this */
use common\components\helpers\HTools;

$data=$this->getCalendarData();
?>
<div class="reserve-separator a-hidden slow wow fadeIn separator a-hidden slow wow fadeIn"></div>
  <div class="container">
    <div class="reserve-inner">
      <h2 class="wow bounceInLeft slower a-hidden">Забронируйте время игры <span>прямо сейчас!</span></h2>
      <div class="reserve-calendar wow bounceInRight slower a-hidden">
        <div class="reserve-calendar-slider">
        <?php foreach($data as $item): ?>
          <div class="reserve-month">
            <div class="reserve-month-head">
              <a href="#" class="reserve-month-prev">
                <i class="fas fa-chevron-left"></i>
                <span><?= $item['months']['prev']; ?></span>
              </a>
              <div class="reserve-month-current">
                <span><?= $item['months']['current']; ?></span>
              </div>
              <a href="#" class="reserve-month-next">
                <span><?= $item['months']['next']; ?></span>
                <i class="fas fa-chevron-right"></i>
              </a>
            </div>
            <div class="reserve-days">
              <div class="reserve-days-title">
                <div class="reserve-col">
                  <div class="reserve-days-title-item">Пн</div>
                </div>
                <div class="reserve-col">
                  <div class="reserve-days-title-item">Вт</div>
                </div>
                <div class="reserve-col">
                  <div class="reserve-days-title-item">Ср</div>
                </div>
                <div class="reserve-col">
                  <div class="reserve-days-title-item">Чт</div>
                </div>
                <div class="reserve-col">
                  <div class="reserve-days-title-item">Пт</div>
                </div>
                <div class="reserve-col">
                  <div class="reserve-days-title-item">Сб</div>
                </div>
                <div class="reserve-col">
                  <div class="reserve-days-title-item">Вс</div>
                </div>
              </div>
              <div class="reserve-days-list">
              	<?php for($i=1; $i<$item['first_day_week']; $i++): ?>
                <div class="reserve-col">
                  <div class="reserve-days-item empty"></div>
                </div>
                <?php endfor; ?>
                <?php for($day=1; $day<($item['days_count']+1); $day++): ?>
                	<?php if(empty($item['days'][$day]) || (empty($item['days'][$day]['free_ticket_count']))): ?>
                	<div class="reserve-col">
                      <div class="reserve-days-item reserved">
                        <div class="reserve-days-number"><?= $day; ?></div>
                        <div class="reserve-days-places"></div>
                      </div>
                    </div>
                	<?php else: $dayData=$item['days'][$day]; ?>
                    <div class="reserve-col">
                      <a href="#" data-src="/extend/booking/default/getBookingForm?date=<?= $dayData['date'] ?>&<?=time()?>" data-type="ajax" data-touch="false" data-fancybox class="reserve-days-item">
                        <div class="reserve-days-number"><?= $day; ?></div>
                        <div class="reserve-days-places"><span><?= $dayData['free_ticket_count']; ?></span> <?=HTools::pluralLabel($dayData['free_ticket_count'], ['место', 'места', 'мест'])?></div>
                      </a>
                    </div>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php for($i=1; $i<(7 - $item['last_day_week']); $i++): ?>
                <div class="reserve-col">
                  <div class="reserve-days-item empty"></div>
                </div>
                <?php endfor; ?>
              </div>
            </div>
          </div>
      <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="reserve-separator a-hidden slow wow fadeIn separator a-hidden slow wow fadeIn"></div>