<?
/**
 * Добавление в корзину
 * POST
 * id - идентификатор товара
 * quantity - кол-во товара (необязательно, по умолчанию +1)
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

\Kontur\Sale\AjaxBasket::add();
?>
