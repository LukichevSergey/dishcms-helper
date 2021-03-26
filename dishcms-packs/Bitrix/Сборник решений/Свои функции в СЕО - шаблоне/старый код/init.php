
https://dev.1c-bitrix.ru/community/blogs/oracle/userdefined-functions-and-seo-infoblock.php

// Дополнительные функции для SEO шаблонов 
if (\Bitrix\Main\Loader::includeModule('iblock'))
{ 
   \Bitrix\Main\EventManager::getInstance()->addEventHandler(
      "iblock",
      "OnTemplateGetFunctionClass",
      array("FunctionMyFunc", "eventHandler")
   ); 

   //подключаем файл с определением класса FunctionBase
   //это пока требуется т.к. класс не описан в правилах автозагрузки
   include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/lib/template/functions/fabric.php");
   class FunctionMyFunc extends \Bitrix\Iblock\Template\Functions\FunctionBase
   {
      public static function eventHandler($event)
      {
         $parameters = $event->getParameters();
         $functionName = $parameters[0];
         if ($functionName === "my_func")
         {
            return new \Bitrix\Main\EventResult(
               \Bitrix\Main\EventResult::SUCCESS,
               "\\FunctionMyFunc"
            );
         }
      }
      public function calculate($parameters)
      {
		 $result = $this->parametersToArray($parameters);
      }
   }
}
