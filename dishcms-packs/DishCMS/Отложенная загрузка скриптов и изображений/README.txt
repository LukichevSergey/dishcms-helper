----------------------------------------------------------
LazyLoad для картинок
----------------------------------------------------------
1) Добавить код в /index.php
Yii::createWebApplication($config)->run();
после...
заменить 

ob_end_flush(); 

на

if(preg_match('/(admin|cp)/i', $_SERVER['REQUEST_URI'])) {
    ob_end_flush();
}
else {
    // lazy load
    $content=ob_get_clean();
    echo preg_replace_callback('#<img([^>]*?) src="([^\s>]+)"([^>]*?)>#i', function($m) {
        if(preg_match('#data-lazyload-disable#', $m[0]) || !preg_match('#^/images/.*$#', $m[2])) { return $m[0]; }
            return '<img'.$m[1].' src="/images/dot-loader.gif" data-lazyload="1" data-src="'.$m[2].'"'.$m[3].'>';
    }, $content);
}

2) Добавить в шаблон main.php перед </body>

<script>window.addEventListener('DOMContentLoaded',function(e){let loaded=false;function loadlazy(){loaded=true;let jqiid=setInterval(function(){if(typeof(jQuery)!="undefined"){
clearInterval(jqiid);let s=document.createElement("script");s.src="//cdn.jsdelivr.net/npm/lazyload@2.0.0-beta.2/lazyload.js";s.type="text/javascript";$('body').append(s);
let lziid=setInterval(function(){if(typeof(lazyload)!="undefined"){clearInterval(lziid);lazyload(jQuery("[data-lazyload]").toArray());
setInterval(function(){lazyload(jQuery("[data-lazyload]").toArray());},1000);}},200);}},200);}
setTimeout(function(){if(!loaded){loadlazy()}},200);$(document).on("scroll mousemove",function(){if(!loaded){loadlazy()}});});</script>

Пример с отложенной подгрузкой для слайдера
<script>window.addEventListener('DOMContentLoaded',function(e){let loaded=false;sliderloaded=false;
function sliderload(){if(!sliderloaded){sliderloaded=true;if($('.slider').length){$('.slider').slideDown();}}}
function loadlazy() {loaded=true;let lazyloadJqueryLoadedIntervalId=setInterval(function(){
if(typeof(jQuery)!="undefined") {clearInterval(lazyloadJqueryLoadedIntervalId);
let s=document.createElement("script");s.src="//cdn.jsdelivr.net/npm/lazyload@2.0.0-beta.2/lazyload.js";s.type="text/javascript";$('body').append(s);
let lazyloadLazyLoadedIntervalId=setInterval(function(){if(typeof(lazyload)!="undefined") {
clearInterval(lazyloadLazyLoadedIntervalId);lazyload(jQuery("[data-lazyload]").toArray());setInterval(function(){lazyload(jQuery("[data-lazyload]").toArray());}, 1000);
}}, 200);}}, 200);}
setTimeout(function(){if(!loaded){loadlazy()}},200);setTimeout(function(){sliderload()},10000);
$(document).on("scroll mousemove",function(){if(!loaded){loadlazy()}sliderload()});});</script>

----------------------------------------------------------
Отложенная подгрузка скриптов
----------------------------------------------------------
Добавить в <head>

<script>window.__jsLazyLoadIntialized=false;window.__jsLazyLoadIntervalId=setInterval(function(){if(typeof $!='undefined'){clearInterval(window.__jsLazyLoadIntervalId);window.__jsLazyLoadIntialized=true;}},100);
window.__jsLazyLoad=function(src,delay,attrs){let loaded=false,intervalId=setInterval(function(){if(window.__jsLazyLoadIntialized){clearInterval(intervalId);function load(){if(!loaded){loaded=true;let s=document.createElement("script");s.type='text/javascript';s.src=src;if(typeof attrs=='undefined'){attrs={}}for(let a in attrs){s.setAttribute(a,attrs[a]);}$('body').append(s);}}
if((typeof delay=="undefined")||(delay===null)){$(document).on("scroll mousemove",function(){load();});}else{setTimeout(function(){load();},delay);}}},100);};
window.__jsLazyLoadScript=function(func,delay){let loaded=false,intervalId=setInterval(function(){if(window.__jsLazyLoadIntialized){clearInterval(intervalId);function load(){if(!loaded){loaded=true;func();}}
if(typeof delay=="undefined"){$(document).on("scroll mousemove",function(){load();});}else{setTimeout(function(){load();},delay);}}},100);};</script>

-----------------
Использование

<script>window.__jsLazyLoad("//ссылка_на_подключаемый_скрипт");</script>

<script>window.__jsLazyLoadScript(function(){
... код скрипта ...
});</script>

<script>window.__jsLazyLoadScript(function(){$('body').append(``);});</script>