<?
if(!function_exists('__pt')) { /* Функция измерения быстродействия */ function __pt($param=null) { static $last=null; static $times=[]; $m=microtime(true); $m2=explode('.',(string)$m);
if($param === true) { global $USER; if($USER->IsAdmin()){$html='<pre style="display:block;position:fixed;background:#fff;color:#000;border:1px solid #000;z-index:99999;top:0;right:5px;padding:5px;font-size:12px;line-height:14px;"><b>Время выполнения:</b></br>'; 
foreach($times as $time) { $html.=$time[0] . ', time: ' . $time[1]. ($time[2]!==null ? " ({$time[2]})" : '') . '<br/>'; }; $html.='</pre>'; echo $html;} return; }
$times[]=[date('H:i:s', $m) . '.' . $m2[1], ($last ? $m-$last : null), $param]; $last=$m; }}
