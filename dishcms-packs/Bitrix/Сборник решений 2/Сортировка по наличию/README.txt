В init.php подключить обработчик
require_once dirname(__FILE__) . '/kontur/handlers/UpdateCatalogQuantityHandler.php';
\kontur\handlers\UpdateCatalogQuantityHandler::init(<CATALOG IBLOCK ID>, <OFFERS IBLOCK ID>);

Для первичного обновления количества товара добавить
\kontur\handlers\UpdateCatalogQuantityHandler::updateAll(<HASH>);