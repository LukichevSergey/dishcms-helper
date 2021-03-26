function custommouseclick(e) {
    if (document.all) {    // IE
       // Чтобы отключить левую или среднюю кнопку поставьте цифру 1
      if (event.button == 2) {return false;}
    }
    if (document.layers) { // NC
      if (e.which == 3) {return false;}
    }
}
if (document.layers){document.captureEvents(Event.MOUSEDOWN);}
document.onmousedown=custommouseclick;
document.oncontextmenu=function(e){return false};
