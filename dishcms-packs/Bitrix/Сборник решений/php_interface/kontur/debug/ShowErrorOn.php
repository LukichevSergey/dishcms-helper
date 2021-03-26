<?
if(!function_exists("KonturDebugShowErrorOn"))
{
    function KonturDebugShowErrorOn()
    {
    	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE & ~E_DEPRECATED);
    	ini_set('display_errors','On');
  	}
}
if(!function_exists("kdbg"))
{
    function kdbg()
    {
    	KonturDebugShowErrorOn();
    }
}
