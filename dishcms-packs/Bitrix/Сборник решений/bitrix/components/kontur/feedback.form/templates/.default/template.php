<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
use Bitrix\Main\Application;

$formId=$arParams['KFF_FORM_ID'];
$formAction=Application::getInstance()->getContext()->getRequest()->getRequestUri();

?><script>document.addEventListener("DOMContentLoaded",function(){KFF_InitForm("<?=$formId?>","<?=$arParams['KFF_FORM_HASH']?>");});</script>
<div class="feedback-form-wrap tac">
    <div class="feedback-form dib">
        <form id="<?=$formId?>" action="<?=$formAction?>" method="POST">
            <div class="row">
                <div class="col-sm-5 col-md-5">
                    <div class="feedback-form-left tal">                        
                        <p>Вы можете  оставить заявку  или задать нам вопрос через данную форму</p>
                        <div class="fb-form-item fbl1 tal">
                            <input type="radio" id="fbt-1" name="<?=$formId?>[TYPE]" value="QUESTION"><label for="fbt-1"><span>Задать вопрос</span></label>
                        </div>
                        <div class="fb-form-item fbl2 tal">
                            <input type="radio" id="fbt-2" name="<?=$formId?>[TYPE]" value="REQUEST"><label for="fbt-2"><span>Подать заявку</span></label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-7 col-md-7">
                    <div class="fb-form-right tal">
                        <div class="fb-form-item">
                            <div class="row">
                                <div class="col-sm-4 tar"><label for="">Ваше имя *</label></div>
                                <div class="col-sm-8"><input type="text" name="<?=$formId?>[NAME]" placeholder="Имя" class="fb-inp"></div>
                            </div>
                            
                        </div>
                        <div class="fb-form-item">
                            <div class="row">
                                <div class="col-sm-4 tar"><label for="">Телефон</label></div>
                                <div class="col-sm-8"><input type="text" name="<?=$formId?>[PHONE]"  placeholder="+7 (___) -____-__-__" class="fb-inp"></div>
                            </div>
                            
                        </div>
                        <div class="fb-form-item">
                            <div class="row">
                                <div class="col-sm-4 tar"><label for="">E-mail *</label></div>
                                <div class="col-sm-8"><input type="text" name="<?=$formId?>[EMAIL]" placeholder="E-mail" class="fb-inp"></div>
                            </div>
                            
                        </div>
                        <div class="fb-form-item">
                            <div class="row">
                                <div class="col-sm-4 tar"><label for="">Сообщение </label></div>
                                <div class="col-sm-8"><textarea placeholder="Текст сообщения" name="<?=$formId?>[MESSAGE]" class="fb-inp"></textarea></div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="fb-submit-wrap">
                <button type="submit" class="callback-button">Отправить</button>
            </div>
        </form>
    </div>
</div>