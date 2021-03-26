window.konturCheckPriceBtnComponentTemplateList=function(options) {
    let _this={options:options};
    function j(name){return '.js-checkprice-pricetag-btn-list-' + name;}
    function ajaxurl(){return window.location.pathname + '?' + _this.options.sess;}
    setInterval(function() {
        $.post(ajaxurl(), {ACTION:'PRICETAG_BTN_LIST_GET_COUNT'}, function(r) {
            $(j('count')).text(isNaN(r.count)?'-':r.count);
        }, 'json');
    }, 5000);
    return _this;
};