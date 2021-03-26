<script data-skip-moving="true">window.__jsLazyLoadIntialized=false;window.__jsLazyLoadIntervalId=setInterval(function(){if(typeof $!='undefined'){clearInterval(window.__jsLazyLoadIntervalId);window.__jsLazyLoadIntialized=true;}},100);
window.__jsLazyLoad=function(src,delay,attrs){let loaded=false,intervalId=setInterval(function(){if(window.__jsLazyLoadIntialized){clearInterval(intervalId);function load(){if(!loaded){loaded=true;let s=document.createElement("script");s.type='text/javascript';s.src=src;if(typeof attrs=='undefined'){attrs={}}for(let a in attrs){s.setAttribute(a,attrs[a]);}$('body').append(s);}}
if(typeof delay=="undefined"){$(document).on("scroll mousemove",function(){load();});}else{setTimeout(function(){load();},delay);}}},100);};
window.__jsLazyLoadScript=function(func,delay){let loaded=false,intervalId=setInterval(function(){if(window.__jsLazyLoadIntialized){clearInterval(intervalId);function load(){if(!loaded){loaded=true;func();}}
if(typeof delay=="undefined"){$(document).on("scroll mousemove",function(){load();});}else{setTimeout(function(){load();},delay);}}},100);};</script>

-----------------
Использование

<script>window.__jsLazyLoad("//ссылка_на_подключаемый_скрипт");</script>

<script>window.__jsLazyLoadScript(function(){
... код скрипта ...
});</script>

<script>window.__jsLazyLoadScript(function(){$('body').append('');});</script>