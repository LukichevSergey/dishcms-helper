/**
 * BX Popup Win
 *
 * options
 * {
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
 *      className: (optional: "popup-window-button-cancel"),
 *      click: callback function on click button Close.
 * 	}
 * }
 *
 * Button class names
 * 	popup-window-button-accept
 * 	popup-window-button-create
 * 	popup-window-button-wait
 * 	popup-window-button-text
 * 	popup-window-button-decline
 * 	popup-window-button-cancel
 * 	popup-window-button-link
 * 	popup-window-button-disable
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
 	   autoHide: false,
       offsetLeft: 0,
       offsetTop: 0,
       overlay : true,
       closeByEsc: true,
       titleBar: true,
       closeIcon: {top: '10px', right: '10px'}
    });
 	obPopupWin.setTitleBar({
    	content: BX.create('div', {
	        style: { marginRight: '30px', whiteSpace: 'nowrap' },
    	    text: o("title", "")
	      })
    });
    obPopupWin.setContent(o("text", "")); 
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
}
