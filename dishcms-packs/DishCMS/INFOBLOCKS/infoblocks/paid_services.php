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
                        $html=$data->title;
                        if($data->prop___second_text) {
                            $html.="<br/><small><i>{$data->prop___second_text}</i></small>";
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