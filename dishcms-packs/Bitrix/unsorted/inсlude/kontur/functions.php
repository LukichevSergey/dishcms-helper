<?php
if ( !function_exists("konturUpdateOrderProps") )
{
    /**
     * Обновление свойств заказа.
     * @param string id заказа.
     * @param string id типа плательщика.
     * @param array массив свойств для обновления вида (code=>value).
     */
    function konturUpdateOrderProps($ORDER_ID, $PERSON_TYPE_ID, $arProps)
    {
        $db_order = CSaleOrderPropsValue::GetList();
        if ($arOrder = $db_order->Fetch())
        {
            $db_order_props = CSaleOrderProps::GetList(
                array("SORT" => "ASC"),
                array("PERSON_TYPE_ID"=>$PERSON_TYPE_ID)
            );

            while ($arOrderProps = $db_order_props->Fetch()) {
                if(array_key_exists($arOrderProps["CODE"], $arProps)) {
                    $value=(LANG_CHARSET == 'windows-1251')
                        ? CharsetConverter::GetInstance()->Convert($arProps[$arOrderProps["CODE"]], 'UTF-8', 'windows-1251')
                        : $arProps[$arOrderProps["CODE"]];
                    CSaleOrderPropsValue::Add(array(
                        "ORDER_ID" => $ORDER_ID,
                        "ORDER_PROPS_ID" => $arOrderProps["ID"],
                        "NAME" => $arOrderProps["NAME"],
                        "CODE" => $arOrderProps["CODE"],
                        "VALUE" => $value
                    ));
                }
            }
        }
    }
}

if ( !function_exists("konturGetCaptcha") )
{
    /**
     * Получение параметров каптчи
     * @result array возвращает массив из двух значений Array(CaptchaSid, CaptchaCode)
     */
    function konturGetCaptcha()
    {
        global $APPLICATION;
        global $DB;

        $captchaSid=$APPLICATION->CaptchaGetCode();
        $db_captcha=$DB->Query("SELECT `code` FROM `b_captcha` WHERE `id`='{$captchaSid}'");
        $captchaCode=$db_captcha->Fetch();

        return array($captchaSid, (empty($captchaCode) ? "" : $captchaCode["code"]));
    }
}

if ( !function_exists("konturGetPropertyFile") )
{
    /**
     * Получить данные свойства файл.
     * @param array элемент инфоблока
     * @param string имя свойтва
     */
    function konturGetPropertyFile($arItem, $propName)
    {
        $arFiles=array();
        if(!empty($arItem["PROPERTIES"][$propName]["VALUE"])) {
            foreach($arItem["PROPERTIES"][$propName]["VALUE"] as $fileID) {
                $rsIBlockElement = CIBlockElement::GetByID($fileID);
                $arIBlockElement = $rsIBlockElement->GetNext();

                $rsFile = CFile::GetByID($arIBlockElement["DETAIL_PICTURE"]);
                $arFiles[] = $rsFile->Fetch();
            }
        }
        return $arFiles;
    }
}