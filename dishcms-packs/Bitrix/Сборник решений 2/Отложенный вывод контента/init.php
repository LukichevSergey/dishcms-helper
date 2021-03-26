<?
//$APPLICATION->SetPageProperty("myVarName", "myValue");
//$APPLICATION->AddBufferContent('konturPrintContent', 'myContent', 'myVarName', 'myValue');
function konturPrintContent($content, $name, $value)
{
    global $APPLICATION;
    if($APPLICATION->GetPageProperty($name) === $value) {
        return $content;
    }
}

