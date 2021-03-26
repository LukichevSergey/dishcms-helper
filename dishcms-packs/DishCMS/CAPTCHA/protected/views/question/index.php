<?php CmsHtml::fancybox(); ?>

<span id="add-question">
    <a>Задайте свой вопрос</a>
</span>

<h1>Вопрос-ответ</h1>

<div id="question-list" class="question-list">
    <?php foreach($list as $item): ?>
        <?php if (empty($item->answer)) continue; ?>
        <?$collapsed=D::cms('question_collapsed')?' collapsed':'';?>
        <div class="item">
            <span class="username"><?php echo $item->username; ?></span>
            <a class="question<?=$collapsed?>"><?php echo $item->question; ?></a>
            <div class="answer<?=$collapsed?>"><?php echo $item->answer ?></div>
        </div>
    <?php endforeach; ?>

    <?php if (!$list): ?>
    <p>Нет вопросов</p>
    <?php endif; ?>
</div>

<div style="display: none">
    <?php $this->renderPartial('_form', compact('model')); ?>
</div>

<script type="text/javascript">
    $(function() {
        $('#question-list .question').click(function() {
            $(this).next().toggleClass('show');
        });

        $('#add-question a').click(function() {
            $.fancybox({
                'href': '#question-form-div',
                'scrolling': 'no',
                'titleShow': false,
                'onComplete': function(a, b, c) {
                    $('#fancybox-wrap').addClass('formBox');
                }
            });
        });
    });

    function submitForm(form, hasError) {
        if (!hasError) {
            $.post($(form).attr('action'), $(form).serialize(), function(data) {
                if (data == 'ok')
                    $('#question-form-div').html('<h2>Ваш вопрос отправлен</h2>');
                else
                    $('#question-form-div').html('<h2>При отправке вопроса возникла ошибка</h2>');
            });
        }
    }
</script>
