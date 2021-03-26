window.konturCheckPriceComponent=function() {
    let _this={options:{}, cache:{}};
    function j(name){return '.js-kontur-checkprice-' + name;}
    function showWait(){return BX.showWait('snapTabControl_layout');}
    function closeWait(wait){BX.closeWait('snapTabControl_layout', wait);}
    function ajaxurl(){return window.location.pathname + '?' + _this.options.sess;}
    _this.init=function(options) { 
        _this.options=options; 
        $('#snapTabControl_layout').find('.adm-detail-tabs-block span').attr('title', '');
    }
    _this.send=function(data) {
        $(j('workarea')).hide();
        $.post(ajaxurl(), data, function(r) {
            $(j('progress-box')).html(r.html);
            if(typeof r.data != 'undefined'){setTimeout(function(){_this.send(r.data);}, _this.options.delay * 1000);}
        },'json');
    };
    _this.csend=function(data, callback) {
        $.post(ajaxurl(), data, callback, 'json');
    };
    _this.checkTab=function(snapId1, snapId2) {
        let loadingBox=$('#snapTabControl_layout').find('.adm-detail-content:visible').find(j('data-loading'));
        if(loadingBox.length) {
            let wait=showWait();
            _this.csend({ACTION:'LOAD', SNAP1:snapId1, SNAP2:snapId2}, function(r) {
                loadingBox.parent().html(r.html);
                closeWait(wait);
            });
        }
    };
    $(document).on('click', j('action'), function(e) {
        let action=$(e.target).data('action'),message='';
        if(action=='SNAP'){message='Запускается процесс формирования цен, пожалуйста подождите...';}
        else{$message='пожалуйста подождите...';}
        $(j('progress-box')).html('<p>'+message+'</p>');
        $(j('progress-box')).show();        
        _this.send({ACTION: $(e.target).data('action')});
    });
    $(document).on('click', j('btn-snapview'), function(e) {
        let a=$(e.target).closest('a'), snap1=a.data('snap1'), snap2=a.data('snap2');
        let boxName=('snapview-box-' + snap1 + '-' + snap2), box=$(j(boxName)); a.hide();  
        if(box.length > 0) { 
            if(box.is(':hidden')) { a.text('свернуть'); box.html(_this.cache[boxName]); box.show(); a.show(); } 
            else { a.text('показать'); box.html(''); box.hide(); a.show(); }
        }
        else {
            let wait=showWait();
            $.post(ajaxurl(), {ACTION:'LOAD', SNAP1:snap1, SNAP2:snap2}, function(r) {
                let tr=a.parents('tr:first');_this.cache[boxName]='<td colspan="2">' + r.html + '</td>';
                tr.after('<tr class="kontur-checkprice-snapview-box js-kontur-checkprice-' + boxName + ' ' + tr.attr('class') + '">' + _this.cache[boxName] + '</tr>');
                closeWait(wait);
                a.text('свернуть');a.show();
            },'json');
        }
    });
    $(document).on('click', j('btn-pricetag'), function(e) {
        let btn=$(e.target), item=btn.data('item'), add=(btn.attr('data-added') != 'Y');
        btn.addClass('ui-btn-clock');
        _this.csend({ACTION:'PRICETAG', ID:item, ADD:+add}, function(r) {
            if(add){btn.addClass('ui-btn-success-light');}else{btn.removeClass('ui-btn-success-light');}
            btn.attr('data-added', add?'Y':'N').text(add?'Ценник добавлен':'Добавить ценник');
            btn.removeClass('ui-btn-clock');
        });
    });
    return _this;
};