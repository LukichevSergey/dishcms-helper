window.konturCheckPricePriceTagComponent=function(options) {
    let _this={};
    function j(name){return '.js-kontur-checkprice-pricetag-' + name;}
    function showWait(){return BX.showWait('kontur-chekcprice-table-tagprice-list');}
    function closeWait(wait){BX.closeWait('kontur-chekcprice-table-tagprice-list', wait);}
    function ajaxurl(){return window.location.pathname + '?' + options.sess;}
    $(document).on('click', j('btn-remove'), function(e) {
        let btn=$(e.target), item=btn.data('item');
        btn.addClass('ui-btn-clock');
        $.post(ajaxurl(), {ACTION:'REMOVE_PRICETAG', ID:item}, function(r) {
            let tr=btn.parents('tr:first'),productId=+tr.data('product'),table=tr.parents('table:first');
            if(!isNaN(productId) && (table.find('[data-product='+productId+']').length === 2)){table.find('[data-product='+productId+']').remove();}
            else{tr.remove();}
        }, 'json');
    });
    $(document).on('click', j('btn-print'), function(e) {
        let btn=$(e.target);btn.addClass('ui-btn-disabled ui-btn-clock');showWait();
        window.konturCheckPricePriceTagComponentPrintBtn=btn;
        if($('body').find(j('print-frame')).length) { $('body').find(j('print-frame')).remove(); }
        $('body').append(`<iframe src="${ajaxurl()}&print=Y" onload="window.konturCheckPricePriceTagComponentPrintLoaded()"
            class="js-kontur-checkprice-pricetag-print-frame" style="display:none !important"></iframe>`);
    });
    window.konturCheckPricePriceTagComponentPrintLoaded=function(){
        window.konturCheckPricePriceTagComponentPrintBtn.removeClass('ui-btn-disabled ui-btn-clock');closeWait();
    };
    return _this;
};