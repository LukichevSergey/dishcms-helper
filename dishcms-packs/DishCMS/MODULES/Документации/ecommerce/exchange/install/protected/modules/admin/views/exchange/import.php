<?php
/** @var \ExchangeController $this */

$this->renderPartial('ecommerce.modules.exchange.modules.admin.views.exchange.import', [
    'iterator'=>'ecommerce.modules.exchange.config.iterators.excel_import.main', 
    'config'=>'application.config.exchanges.ecommerce_import'
]);
