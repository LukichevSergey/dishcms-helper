<? 
/**
 * Г€Г¬ГЇГ®Г°ГІ ГЄГ ГІГ Г«Г®ГЈГ  ГЁГ§ xml-ГґГ Г©Г«Г®Гў CML2.0 ГЎГҐГ§ ГЇГ°ГїГ¬Г®ГЈГ® Г®ГЎГ¬ГҐГ­Г  Г± 1Г‘ 8.1
 * http://dev.1c-bitrix.ru/community/webdev/group/78/blog/1654/
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); 
header("Content-type:text/html; charset=windows-1251");
if(isset($_SESSION["BX_CML2_IMPORT"])) unset($_SESSION["BX_CML2_IMPORT"]);
$_SESSION["BX_CML2_IMPORT"]["NS"]["STEP"]=0;
//\kontur\bx1c\Convert::run( '/upload/1c_catalog/import.zip', '/upload/1c_catalog', 'windows-1251', false );
?>
<html>
<a  href="javascript:start('import.xml')">import import.xml</a> 
<a href="javascript:start('offers.xml')">import offers.xml</a> 
<a href="javascript:start('company.xml')"> import company.xml</a> 
<a style='color:red;' href="javascript:reset()">reset step</a> 
<a style='color:red;' href="javascript:status='stop'">stop import</a><hr> 
<div id='main' style='display:none;width:400;font-size:12;border:1px solid #ADC3D5; padding:5'> 
<div id="log"></div> 
<div align=right id="load"></div> 
</div> 
<div id="timer"></div>

<script>
var 
log=document.getElementById("log"); 
timer=document.getElementById("timer"); 
load=document.getElementById("load"); 
var zup_import=false; 
//ГЇГҐГ°ГҐГ¬ГҐГ­Г­Г»ГҐ ГІГ Г©Г¬ГҐГ°Г  
m_second=0; 
seconds=0; 
minute=0; 
//ГЇГҐГ°ГҐГ¬ГҐГ­Г­Г»ГҐ ГЁГ¬ГЇГ®Г°ГІГ  
i=1; 
a=''; 
proccess=true; 
status="continue"; 
function createHttpRequest() 
   { 
   var httpRequest; 
      if (window.XMLHttpRequest) 
      httpRequest = new XMLHttpRequest();   
      else if (window.ActiveXObject) {     
      try { 
      httpRequest = new ActiveXObject('Msxml2.XMLHTTP');   
      } catch (e){}                                   
      try {                                           
      httpRequest = new ActiveXObject('Microsoft.XMLHTTP'); 
      } catch (e){} 
      } 
   return httpRequest; 
}
function start(file) 
      { 
      document.getElementById("main").style.display='block'; 
      load.innerHTML="<b>Г‡Г ГЈГ°ГіГ§ГЄГ </b>...<img align='center'                 src='http://gifanimation.ru/images/ludi/17_3.gif' width='30'/>" 
             i=1; 
      a=""; 
      m_second=0; 
        seconds=0; 
      proccess=true; 
      start_timer(); 
      timer.innerHTML=""; 
      if (file=="company.xml") {zup_import=true;} 
      log.innerHTML="<b>Г€Г¬ГЇГ®Г°ГІ "+file+"</b><hr>"; 
      query_1c(file) 
      }
	  
	  function query_1c(file) 
      { 
      var import_1c=createHttpRequest(); 
      if (zup_import==true) 
      { 
      r="/bitrix/admin/1c_intranet.php?type=catalog&mode=import&filename="+file; 
      } else{r="/bitrix/admin/1c_exchange.php?type=catalog&mode=import&filename="+file;} 
                     load.style.display="block"; 
            import_1c.open("GET", r, true); 
      import_1c.onreadystatechange = function() 
            { 
            a=log.innerHTML; 
            if (import_1c.readyState == 4 && import_1c.status == 0) 
                  { 
                  error_text="<em>ГЋГёГЁГЎГЄГ  Гў ГЇГ°Г®Г¶ГҐГ±Г±ГҐ ГўГ»ГЈГ°ГіГ§ГЄГЁ</em><div style='width:270;font-size:11;border:1px solid             black;background-color:#ADC3D5;padding:5'>Г‘ГҐГ°ГўГҐГ° ГіГЇГ Г« ГЁ Г­ГҐ ГўГҐГ°Г­ГіГ« Г§Г ГЈГ®Г«Г®ГўГЄГ®Гў.</div>" 
                     log.innerHTML=a+"Г�Г ГЈ "+i+": "+error_text; 
                     load.style.display="none"; 
                     status="continue" 
                     alert("Import is crashed!"); 
                  } 
             
                  if (import_1c.readyState == 4 && import_1c.status == 200)   
                     { 
                        if ((import_1c.responseText.substr(0,8 )!="progress")&&(import_1c.responseText.substr(0,7)!="success")) 
                        { 
                           error_text="<em>ГЋГёГЁГЎГЄГ  Гў ГЇГ°Г®Г¶ГҐГ±Г±ГҐ ГўГ»ГЈГ°ГіГ§ГЄГЁ</em><div style='width:270;font-size:11;border:1px solid black;background-color:#ADC3D5;padding:5'>"+import_1c.responseText+"</div>" 
                           log.innerHTML=a+"Г�Г ГЈ "+i+": "+error_text; 
                           status="error"; 
                        } 
                        else 
                        { 
                           n=import_1c.responseText.lastIndexOf('s')+1; 
                           l=import_1c.responseText.length; 
                           mess=import_1c.responseText.substr(n,l); 
                           log.innerHTML=a+"Г�Г ГЈ "+i+": "+mess+" ("+seconds+" Г±ГҐГЄ.)"+"<br>"; 
                           seconds=0; 
                           load.style.display="none"; 
                           i++; 
                        } 
                        if ((import_1c.responseText.substr(0,7)=="success")||(status=="error")||(status=="stop")) 
                        { 
                           load.style.display="none"; 
                           status="continue" 
                           proccess=false; 
                           timer.innerHTML="<hr>Г‚Г°ГҐГ¬Гї ГўГ»ГЈГ°ГіГ§ГЄГЁ: <b>"+minute+" Г¬ГЁГ­. "+m_second+" Г±ГҐГЄ.</b>"; 
                        } 
                        else 
                        { 
                           query_1c(file); 
                        } 
                     } 
                   
                   
            }; 
import_1c.send(null); 
      }
	  function start_timer() 
      { 
         if (m_second==60) 
         { 
         m_second=0; 
         minute+=1; 
         } 
         if (proccess==true) 
         { 
         seconds+=1; 
         m_second+=1; 
         setTimeout("start_timer()",1000); 
      } 
      } 
       
function reset() 
            { 
            var rest=createHttpRequest(); 
               q="bx_1c_import_lite.php"; 
               rest.open("GET", q, true); 
               rest.onreadystatechange=function() 
                        { 
                        if (rest.readyState == 4 && rest.status == 200)   
                           alert("Г�Г ГЈ ГЁГ¬ГЇГ®Г°ГІГ  Г®ГЎГ­ГіГ«ВёГ­!"); 
                        } 
                
               rest.send(null); 
                
            }       
</script>

<style>
a {
   text-decoration: none;
   color:#36648B;
   background:#FFEFD5;	
   font-size:13;
   padding:5;
   font-family:Arial;
   border:1px dashed #ADC3D5;
   } 
   
</style>
</html>
