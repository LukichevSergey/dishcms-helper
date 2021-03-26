/**
 * @param object options. Параметры:
 *	btnId: string,
 *	popupId: string, 
 *	popupTitle: string,
 *	popupBtnOkEnable: boolean,
 *	popupBtnOkLabel: string,
 *	popupBtnOkClassName: string,
 *	popupBtnOkClick: callable,
 *	popupBtnCancelLabel: string
 *	popupBtnCancelClassName: string
 *	popupOptions: object
 */
function KBTN_BXPOPUP_Init(options) 
{
	$(document).on("click", "#" + options.btnId, function(e) {
        e.preventDefault();
        var $btn=$(e.target).closest("#"+options.btnId);
        
        $.post($btn.attr("href"), function(response) {
            var popupOptions;
            if(options.popupOptions) popupOptions=options.popupOptions;
            else popupOptions={};
            
            popupOptions["id"]=options.popupId;
            popupOptions["title"]=options.popupTitle;
            popupOptions["text"]=response;
            popupOptions["btnClose"]={text:options.popupBtnCancelLabel};
            popupOptions["btnOk"]=false;
                        
            if(options.popupBtnCancelClassName) {
                popupOptions.btnClose["className"]=options.popupBtnCancelClassName;
            }
            if(options.popupBtnOkEnable) {
                popupOptions.btnOk={ text: options.popupBtnOkLabel };
            }
            if(options.popupBtnOkClassName) {
                popupOptions.btnOk["className"]=options.popupBtnOkClassName;
            }
            if(options.popupBtnOkClick) {
                popupOptions.btnOk["click"]=options.popupBtnOkClick;
            }
            
            kontur_bx_popup(popupOptions);
        });
        
        return false;
    });
}
