<?php
return [
    'admin'=>[
        'gridview'=>[
            'columns'=>[
                [
                    'name'=>'id',
                    'header'=>'#',
                    'headerHtmlOptions'=>['style'=>'width:5%;text-align:center'],
                    'htmlOptions'=>['style'=>'text-align:center'],
                ],
                [
                    'type'=>'raw',
                    'header'=>'Изображение',
                    'headerHtmlOptions'=>['style'=>'width:20%;text-align:center'],
                    'htmlOptions'=>['style'=>'text-align:center'],
                    'value'=>function($data) {
                        if($data->imageBehavior->exists()) {
                            return $data->imageBehavior->img(220, 70);
                        }
                        else {
                            return 'изображения нет';
                        }
                    }
                ],
                [
                    'type'=>'raw',
                    'name'=>'title',
                    'header'=>'Информация',
                    'value'=>function($data) {
                        $data->load_fields();
                        echo \CHtml::tag('div', ['style'=>'font-size:1.4em;'], $data->title);
                        echo '<small>';
                        echo '<b>Ссылка:</b> ' . ($data->prop___url ?: 'не указана');
                        echo '<br/><b>Заголовок слайда:</b> ' . ($data->prop___title ?: 'не указан');
                        echo '<br/><b>Описание слайда:</b> ' . ($data->prop___desc ?: 'не указано');
                        echo '</small>';
                    }
                ],
                [
                    'name'=>'sort',
                    'header'=>'Порядок',
                    'headerHtmlOptions'=>['style'=>'width:5%;text-align:center'],
                    'htmlOptions'=>['style'=>'text-align:center'],                    
                ],
                [
                    'type'=>'raw',
                    'name'=>'active',
                    'header'=>'Активен',
                    'headerHtmlOptions'=>['style'=>'width:5%;text-align:center'],
                    'htmlOptions'=>['style'=>'text-align:center'],
                    'value' => function ($data) {
                        if($data->active) {
                            return \CHtml::tag('span', ['class'=>'label label-success'], 'да');
                        }
                        else {
                            return \CHtml::tag('span', ['class'=>'label label-danger'], 'нет');
                        }
                    }
                ],
                'buttons'=>true,
            ]
        ]
    ]
];