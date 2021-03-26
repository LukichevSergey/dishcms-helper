<?php
/** @var \ExchangeController $this */

$this->renderPartial('ecommerce.modules.exchange.modules.admin.views.exchange.export', [
    'iterator'=>'ecommerce.modules.exchange.config.iterators.excel_export.main',
    'config'=>'application.config.exchanges.ecommerce_export'
]);