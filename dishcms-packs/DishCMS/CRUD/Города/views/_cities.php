<div class="header__city">
                                    <span class="header__city-title">Ваш город </span>
                                    <?php $city=City::getCurrentCity(); ?>
                                    <a href="javascript:;" class="header__city-current"><?= $city ? $city->title : 'Выберите город'; ?><i>▼</i></a>
                                    <ul class="header__city-list"><?php
                                        foreach(City::getCities(true) as $title=>$url): 
                                            ?><li class="header__city-item"><?= \CHtml::link($title, ($url?: '#!'), ['class'=>'header__city-link']); ?></li><?php 
                                        endforeach;
                                    ?></ul>
                                </div>

