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
                    'headerHtmlOptions'=>['style'=>'width:15%;text-align:center'],
                    'htmlOptions'=>['style'=>'text-align:center'],
                    'value'=>function($data) {
                        if($data->imageBehavior->exists()) {
                            return $data->imageBehavior->img(120, 120);
                        }
                        else {
                            return 'изображения нет';
                        }
                    }
                ],
                [
                    'type'=>'raw',
                    'name'=>'title',
                    'header'=>'Наименование',
                    'value'=>function($data) {
                        $data->load_fields();
                        $html=\CHtml::tag('div', ['style'=>'font-size:1.4em;margin-bottom:10px'], $data->title);
                        foreach([2,3,4,5] as $n) { 
                            $bn="prop___image_{$n}PropertyBehavior";
                            if($data->$bn->exists()) {
                                $html.=$data->$bn->img(80, 80, true, ['style'=>'margin-right:10px']);
                            }
                        }
                        return $html;
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