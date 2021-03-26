/**
 * BX Popup Win
 *
 * options
 * {
 *	enableScripts: (optional: true) run content scripts (use jQuery)
 * 	id: (optional: "konturpopupwin")popup id,
 * 	title: popup title,
 * 	text: content,
 * 	btnOk: (optional: null) {
 * 		text: (optional: "Ok") button text,
 * 		className: (optional: "popup-window-button-accept"),
 * 		click: callback function on click button OK.
 * 	},
 * 	btnClose: {
 * 		text: (optional: "Close") button text,
 *         className: (optional: "popup-window-button-cancel"),
 *         click: callback function on click button Close.
 * 	}
 * }
 *
 */
function kontur_bx_popup(options) 
{
	function o(name, def, custom) { 
		if(typeof(custom) !== "undefined") {
			return (typeof(custom[name]) === "undefined") ? def : custom[name];
		}
		return (typeof(options[name]) === "undefined") ? def : options[name];
	}

	var obPopupWin = BX.PopupWindowManager.create(o("id", "konturpopupwin"), null, {
 	   autoHide: o("autoHide", false),
       offsetLeft: o("offsetLeft", 0),
       offsetTop: o("offsetTop", 0),
       overlay : o("overlay", true),
       closeByEsc: o("closeByEsc", true),
       titleBar: o("titleBar", true),
       closeIcon: o("closeIcon", {top: '10px', right: '10px'})
    });
 	obPopupWin.setTitleBar({
    	content: BX.create('div', {
	        style: { marginRight: '30px', whiteSpace: 'nowrap' },
    	    text: o("title", "")
	      })
    });

    if((typeof(options.enableScripts) == 'undefined') || options.enableScripts) {
	    var wrapperId="id_" + Math.random().toString(36).slice(20);
	    obPopupWin.setContent('<div id="'+wrapperId+'" class="bxpopup_content_wrapper"></div>');
	    $("#"+wrapperId).html(o("text", ""));
    }
    else {
    	obPopupWin.setContent(o("text", ""));
    }

	var buttons=[];
	if(o("btnOk", false)) {
		buttons.push(new BX.PopupWindowButton({
        	text: o("text", "Ok", o("btnOk", {})),
            className: o("className", "popup-window-button-accept", o("btnOk", {})),
            events: {
				click: o("click", function() { this.popupWindow.close(); }, o("btnOk", {}))
            }
      	}));
	}
	buttons.push(new BX.PopupWindowButton({
        text: o("text", "Close", o("btnClose", {})),
        className: o("className", "popup-window-button-cancel", o("btnClose", {})),
        events: {
    	    click: o("click", function() { this.popupWindow.close(); }, o("btnClose", {}))
	    }
    }));
	obPopupWin.setButtons(buttons);
    obPopupWin.show();

    // run content scripts
//    $("#"+wrapperId).find('script').each(function() {
//    	eval(this.innerHTML);
//    });
}
