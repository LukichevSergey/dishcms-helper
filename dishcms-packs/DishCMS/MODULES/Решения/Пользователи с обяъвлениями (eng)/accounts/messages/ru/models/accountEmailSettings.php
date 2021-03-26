<?php
return [
    'default.subject'=>'New message from #SITENAME#',
    
    // reg_successed
    'default.reg_successed_email_subject'=>'Congratulations! Are you registered!',
    'default.reg_successed_email_body'=>'
        <h1>Congratulations! Are you registered!</h1>
        <p>
            After checking your details by a moderator, your account will be activated.
        </p>
        <p>
            Regards,<br/>
            Asw Aero Team.
        </p>
    ',
    
    // reg_activated
    'default.reg_activated_email_subject'=>'Your access is activated!',
    'default.reg_activated_email_body'=>'
        <h1>Congratulations, your account has been activated!</h1>
        <p>
            <strong>Authrization URL:</strong> #URL_AUTH#<br/>
            <strong>Login:</strong> #EMAIL#<br/>
            <strong>Password:</strong> &lt;Your password&gt;
        </p>
        <p>
            Regards,<br/>
            Asw Aero Team.
        </p>
    ',
    
    // reg_confirm
    'default.reg_confirm_email_subject'=>'Your access is activated!',
    'default.reg_confirm_email_body'=>'
        <h1>Congratulations, your account has been activated!</h1>
        <p>
            <strong>Authrization URL:</strong> #URL_AUTH#<br/>
            <strong>Login:</strong> #EMAIL#<br/>
            <strong>Password:</strong> &lt;Your password&gt;
        </p>
        <p>
            Regards,<br/>
            Asw Aero Team.
        </p>
    ',
    
    // restore_password
    'default.restore_password_email_subject'=>'Your password recovery link on the site #SITENAME#',
    'default.restore_password_email_body'=>'
        <h1>Your link to reset your password on the site #SITENAME#</h1>
        <p>
            To recover your password, follow the link: #URL_RESTORE_PASSWORD#
        </p>
        <p>
            Regards,<br/>
            Asw Aero Team.
        </p>
    ',
    
    // restore_password
    'default.restore_password_email_subject'=>'Your password recovery link on the site #SITENAME#',
    'default.restore_password_email_body'=>'
        <h1>Your link to reset your password on the site #SITENAME#</h1>
        <p>
            To recover your password, follow the link: #URL_RESTORE_PASSWORD#
        </p>
        <p>
            Regards,<br/>
            Asw Aero Team.
        </p>
    ',
    
    // admin_reg_successed
    'default.admin_reg_successed_email_subject'=>'Зарегистрирован новый пользователь',
    'default.admin_reg_successed_email_body'=>'
        <h1>Зарегистрирован новый пользователь!</h1>
        <p>
            <strong>ID:</strong> #ID#<br/>
            <strong>Компания:</strong> #COMPANY#<br/>
            <strong>Категория:</strong> #CATEGORY#<br/>
            <strong>Страна:</strong> #COUNTRY#<br/>
            <strong>Контактное лицо:</strong> #NAME#<br/>
            <strong>Контактный телефон:</strong> #PHONE#<br/>
            <strong>E-mail:</strong> #EMAIL#<br/>
        </p>
    ',
    
    // admin_new_advert
    'default.admin_new_advert_email_subject'=>'Новое объявление ##ID#. Ожидает модерации.',
    'default.admin_new_advert_email_body'=>'
        <h1>Добавлено новое объявление ##ID#. Ожидает модерации.</h1>
        <strong>Пользователь:</strong><br/>
        <p>
            <strong>ID:</strong> #ACCOUNT_ID#<br/>
            <strong>Компания:</strong> #ACCOUNT_COMPANY#<br/>
            <strong>Категория:</strong> #ACCOUNT_CATEGORY#<br/>
            <strong>Страна:</strong> #ACCOUNT_COUNTRY#<br/>
            <strong>Контактное лицо:</strong> #ACCOUNT_NAME#<br/>
            <strong>Контактный телефон:</strong> #ACCOUNT_PHONE#<br/>
            <strong>E-mail:</strong> #ACCOUNT_EMAIL#<br/>
        </p>
        <br/>
        <strong>Объявление (#ADVERT_TYPE#):</strong><br/>
        <p>
            <strong>ID:</strong> #ID#<br/>
            <strong>Part Number:</strong> #PART_NUMBER#<br/>
            <strong>Type of part:</strong> #TYPE_OF_PART#<br/>
            <strong>Quantity:</strong> #QUANTITY#<br/>
            <strong>Condition / Capability Code:</strong> #CODE#<br/>
            <strong>#OBJECT_TYPE#:</strong> #OBJECT_TYPE_VALUE#<br/>
            <strong>Category:</strong> #CATEGORY#<br/>
            <strong>Document:</strong> #DOCUMENT#<br/>
        </p>
    ',
    
    // admin_advert_edited
    'default.admin_advert_edited_email_subject'=>'Изменено объявление ##ID#. Ожидает модерации.',
    'default.admin_advert_edited_email_body'=>'
        <h1>Изменено объявление ##ID#. Ожидает модерации.</h1>
        <strong>Пользователь:</strong><br/>
        <p>
            <strong>ID:</strong> #ACCOUNT_ID#<br/>
            <strong>Компания:</strong> #ACCOUNT_COMPANY#<br/>
            <strong>Категория:</strong> #ACCOUNT_CATEGORY#<br/>
            <strong>Страна:</strong> #ACCOUNT_COUNTRY#<br/>
            <strong>Контактное лицо:</strong> #ACCOUNT_NAME#<br/>
            <strong>Контактный телефон:</strong> #ACCOUNT_PHONE#<br/>
            <strong>E-mail:</strong> #ACCOUNT_EMAIL#<br/>
        </p>
        <br/>
        <strong>Объявление (#ADVERT_TYPE#):</strong><br/>
        <p>
            <strong>ID:</strong> #ID#<br/>
            <strong>Part Number:</strong> #PART_NUMBER#<br/>
            <strong>Type of part:</strong> #TYPE_OF_PART#<br/>
            <strong>Quantity:</strong> #QUANTITY#<br/>
            <strong>Condition / Capability Code:</strong> #CODE#<br/>
            <strong>#OBJECT_TYPE#:</strong> #OBJECT_TYPE_VALUE#<br/>
            <strong>Category:</strong> #CATEGORY#<br/>
            <strong>Document:</strong> #DOCUMENT#<br/>
        </p>
    ',
    
    // admin_advert_response
    'default.admin_advert_response_email_subject'=>'Новый отклик на объявление ##ID#.',
    'default.admin_advert_response_notify_responded'=>'Your ad response was sent successfully.',
    'default.admin_advert_response_notify_not_responded'=>'An error occurred while sending a response to the ad.<br/>Please try again later or contact our technical support.',
    'default.admin_advert_response_email_body'=>'
        <h1>Новый отклик на объявление ##ID#.</h1>
        <strong>Пользователь, который отправил отклик:</strong><br/>
        <p>
            <strong>ID:</strong> #ACCOUNT_ID#<br/>
            <strong>Компания:</strong> #ACCOUNT_COMPANY#<br/>
            <strong>Категория:</strong> #ACCOUNT_CATEGORY#<br/>
            <strong>Страна:</strong> #ACCOUNT_COUNTRY#<br/>
            <strong>Контактное лицо:</strong> #ACCOUNT_NAME#<br/>
            <strong>Контактный телефон:</strong> #ACCOUNT_PHONE#<br/>
            <strong>E-mail:</strong> #ACCOUNT_EMAIL#<br/>
        </p>
        <strong>Пользователь, которому принадлежит объявление:</strong><br/>
        <p>
            <strong>ID:</strong> #ADVERT_ACCOUNT_ID#<br/>
            <strong>Компания:</strong> #ADVERT_ACCOUNT_COMPANY#<br/>
            <strong>Категория:</strong> #ADVERT_ACCOUNT_CATEGORY#<br/>
            <strong>Страна:</strong> #ADVERT_ACCOUNT_COUNTRY#<br/>
            <strong>Контактное лицо:</strong> #ADVERT_ACCOUNT_NAME#<br/>
            <strong>Контактный телефон:</strong> #ADVERT_ACCOUNT_PHONE#<br/>
            <strong>E-mail:</strong> #ADVERT_ACCOUNT_EMAIL#<br/>
        </p>
        <br/>
        <strong>Объявление (#ADVERT_TYPE#):</strong><br/>
        <p>
            <strong>ID:</strong> #ID#<br/>
            <strong>Part Number:</strong> #PART_NUMBER#<br/>
            <strong>Type of part:</strong> #TYPE_OF_PART#<br/>
            <strong>Quantity:</strong> #QUANTITY#<br/>
            <strong>Condition / Capability Code:</strong> #CODE#<br/>
            <strong>#OBJECT_TYPE#:</strong> #OBJECT_TYPE_VALUE#<br/>
            <strong>Category:</strong> #CATEGORY#<br/>
            <strong>Document:</strong> #DOCUMENT#<br/>
        </p>
    ',
    
    // advert_published
    'default.advert_published_email_subject'=>'Your ad ##ID# has been published.',
    'default.advert_published_email_body'=>'
        <h1>Your ad ##ID# has been published.</h1>
        <p>
            You can go to "<a href="#MY_ADVERTS_URL#">My adverts</a>" to view a list of your ads.
        </p>
        <p>
            Regards,<br/>
            Asw Aero Team.
        </p>
    ',
    
    // advert_unpublished
    'default.advert_unpublished_email_subject'=>'Your ad ##ID# has been removed from publication.',
    'default.advert_unpublished_email_body'=>'
        <h1>Your ad ##ID# has been removed from publication.</h1>
        <p>
            You can go to "<a href="#MY_ADVERTS_URL#">My adverts</a>" to view a list of your ads.
        </p>
        <p>
            Regards,<br/>
            Asw Aero Team.
        </p>
    ',
];