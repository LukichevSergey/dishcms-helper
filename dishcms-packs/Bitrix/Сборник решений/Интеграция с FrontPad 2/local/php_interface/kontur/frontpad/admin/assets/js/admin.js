BX.ready(function(){
    BX.bind(BX('frontpad_tab_frontpad_checkall'), 'click', function() {
        var checkAll = BX('frontpad_tab_frontpad_checkall').checked;
        document.querySelectorAll("[name*='DATA[NEW_PRODUCT]']").forEach(function(ch){
            ch.checked = checkAll;
        });
    });
});
