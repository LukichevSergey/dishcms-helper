Получение цены товара при сумме заказа более 10000 руб ($arResult['ID'] - идентификатор товара)
$price10000=\kontur\Catalog::getMinDiscountPrice($arResult['ID'], 10001)
