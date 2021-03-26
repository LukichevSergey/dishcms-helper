<?
if ( !function_exists("KonturSaleAddOrderProperty") )
{
    /**
     * @param int $ORDER_ID id заказа.
     * @param string $PROPERTY_CODE код свойства заказа.
     * @param mixed $VALUE значение свойства заказа.
     */
    function KonturSaleAddOrderProperty($ORDER_ID, $PROPERTY_CODE, $VALUE) 
    {
        if (empty($PROPERTY_CODE)) {
            return false;
        }
        
        if ($arProp = CSaleOrderProps::GetList(array(), array('CODE' => $PROPERTY_CODE))->Fetch()) {
            return CSaleOrderPropsValue::Add(array(
                'NAME' => $arProp['NAME'],
                'CODE' => $arProp['CODE'],
                'ORDER_PROPS_ID' => $arProp['ID'],
                'ORDER_ID' => $ORDER_ID,
                'VALUE' => $VALUE,
            ));
        }
        
        return false;
    }
}

