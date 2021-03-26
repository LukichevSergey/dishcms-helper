<?/***** CRM FORMS ****/?>
<script id="bx24_form_button" data-skip-moving="true">
        function initB24CrmDvsForm(id,sec,b24val) {
            return {"id":id,"lang":"ru","sec":sec,"type":"button","ref":"https://kognitivnyetekhnologii.bitrix24.ru/bitrix/js/crm/form_loader.js","type":"button","click":"","fields":{"values":b24val}};
        }
        var b24paramsload = initB24CrmDvsForm(9,"1ivbpo",{});
        (function(w,d,u,b){w['Bitrix24FormObject']=b;w[b] = w[b] || function(){arguments[0].ref=u;
                (w[b].forms=w[b].forms||[]).push(arguments[0])};
                if(w[b]['forms']) return;
                var s=d.createElement('script');s.async=1;s.src=u+'?'+(1*new Date());
                var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
        })(window,document,'https://kognitivnyetekhnologii.bitrix24.ru/bitrix/js/crm/form_loader.js','b24form');

        b24form(b24paramsload);

        function reinitB24Dvsform(id,sec,nVal) {
            if(!window.Bitrix24FormObject || !window[window.Bitrix24FormObject])
                return;
            if(!window[window.Bitrix24FormObject].forms)
                return;
            // Уничтожаем форму
            Bitrix24FormLoader.unload(b24paramsload);
            // Пересоздаём параметры формы
            b24paramsload = initB24CrmDvsForm(id,sec,nVal);
            // Инициируем форму с новыми данными
            Bitrix24FormLoader.params = b24paramsload;
            Bitrix24FormLoader.init();
            // Открываем попап
            Bitrix24FormLoader.showPopup(b24paramsload);
        }
        function reinitB24Dvsform9(nVal) {
            reinitB24Dvsform(9,"1ivbpo",{"product_1849668":nVal});
        }
        function reinitB24Dvsform11(nVal) {
            reinitB24Dvsform(11,"nsovaf",{"product_1932116":nVal});
        }
</script>
<?/********************/?>
