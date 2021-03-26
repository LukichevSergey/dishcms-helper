<?php
return [
    'default.subject'=>'Новое сообщение с сайта #SITENAME#',
    
    // reg_successed
    'default.reg_successed_email_subject'=>'Вы успешно зарегистрированы на сайте #SITENAME#!',
    'default.reg_successed_email_body'=>'
        <h1>Вы успешно зарегистрированы на сайте #SITENAME#</h1>
        <p>
            <strong>Страница входа в личный кабинет:</strong> #URL_AUTH#<br/>
            <strong>Логин:</strong> #EMAIL#<br/>
            <strong>Пароль:</strong> &lt;Ваш пароль, указанный при регистрации&gt;
        </p>
    ',
    
    // reg_successed
    'default.reg_successed_wholesale_email_subject'=>'Вы успешно зарегистрированы на сайте #SITENAME#!',
    'default.reg_successed_wholesale_email_body'=>'
        <h1>Вы успешно зарегистрированы на сайте #SITENAME#</h1>
        <p>
            После проверки модератором, Ваш аккаунт будет активирован.<sup>*</sup><br/>
            <small><i><sup>*</sup>Вы получите дополнительное почтовое уведомление.</i></small>
        </p>
    ',
    
    // reg_activated
    'default.reg_activated_email_subject'=>'Активирован доступ в личный кабинет на сайте #SITENAME#!',
    'default.reg_activated_email_body'=>'
        <h1>Активирован доступ в личный кабинет на сайте #SITENAME#!</h1>
        <p>
            <strong>Страница входа в личный кабинет:</strong> #URL_AUTH#<br/>
            <strong>Логин:</strong> #EMAIL#<br/>
            <strong>Пароль:</strong> #PASSWORD#
        </p>
    ',
    
    // reg_confirm
    'default.reg_confirm_email_subject'=>'Ваш доступ активирован!',
    'default.reg_confirm_email_body'=>'
        <h1>Ваш доступ активирован!</h1>
        <p>
            <strong>Страница входа в личный кабинет:</strong> #URL_AUTH#<br/>
            <strong>Логин:</strong> #EMAIL#<br/>
            <strong>Пароль:</strong> &lt;Ваш пароль&gt;
        </p>
    ',
    
    // restore_password
    'default.restore_password_email_subject'=>'Восстановление пароля на сайте #SITENAME#',
    'default.restore_password_email_body'=>'
        <h1>Восстановление пароля на сайте #SITENAME#</h1>
        <p>
            Для того, чтобы сменить пароль перейдите по ссылке: #URL_RESTORE_PASSWORD#
        </p>
    ',
    
    // admin_reg_successed
    'default.admin_reg_successed_email_subject'=>'Зарегистрирован новый пользователь на сайте #SITENAME#',
    'default.admin_reg_successed_email_body'=>'
        <h1>Зарегистрирован новый пользователь на сайте #SITENAME#!</h1>
        #AWAITING_MODERATOR_REVIEW#
        <p>
            <strong>ID:</strong> #ID#<br/>
            <strong>Является оптовым покупателем:</strong> #IS_WHOLESALE_BUYER#<br/>
            <strong>Имя:</strong> #NAME#<br/>
            <strong>Фамилия:</strong> #LASTNAME#<br/>
            <strong>E-mail:</strong> #EMAIL#<br/>
            <strong>Контактный телефон:</strong> #PHONE#<br/>
        </p>
    ',    
];