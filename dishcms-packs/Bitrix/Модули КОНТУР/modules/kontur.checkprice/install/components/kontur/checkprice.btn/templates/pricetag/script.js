window.konturCheckPriceBtnComponentTemplatePriceTag=function(options) {
    let _this={options:options,ajax:null,ajaxe:null};
    function j(name){return '.js-checkprice-pricetag-btn-pricetag-' + name;}
    function btn(){return $(j('pricetag'));}
    function id(){return btn().attr('data-item');}
    function ajaxurl(){return window.location.pathname + '?' + _this.options.sess;}
    function btnadd(btn){btn.removeClass('pricetag-added').text('Добавить ценник');}
    function btnremove(btn){btn.addClass('pricetag-added').text('Убрать ценник');}
    function exists(){
        if(_this.ajaxe){_this.ajaxe.abort();}
        _this.ajaxe=$.post(ajaxurl(), {ACTION:'PRICETAG_BTN_EXISTS', ID:id()}, function(r) {
            if(r.exists){btnremove();}else{btnadd();}
        }, 'json');
    }

    $(document).on('click', j('pricetag'), function(e) {
        let btn=$(e.target).closest(j('pricetag')),id=btn.attr('data-item');
        if(_this.ajax){if(btn.parents('.product-detail:first').length){_this.ajax.abort();}}btn.text('обрабатывается...')
        _this.ajax=$.post(ajaxurl(), {ACTION:'PRICETAG_BTN_PRICETAG', ID:id}, function(r) {
            if(r.status=='added'){btnremove(btn);}else{btnadd(btn);}
        }, 'json');
        e.stopImmediatePropagation();
    });

    let itemBox=$(j('pricetag')).parents('.js-element:first');
    if(itemBox.length) {
        let offerId=itemBox.attr('data-curerntofferid');
        if((typeof offerId != 'undefined') && offerId.toString().length) {
            btn().attr('data-item', offerId);exists();
            $(document).on('click','.div_option',function(e){
                let cnt=0,intervalId=setInterval(function(){
                    if(cnt++>100){clearInterval(intervalId);}
                    else if(itemBox.attr('data-curerntofferid') != id()) {
                        btn().attr('data-item', itemBox.attr('data-curerntofferid'));
                        clearInterval(intervalId);exists();
                    }
                }, 100);
            });
        }
    }

    return _this;
};