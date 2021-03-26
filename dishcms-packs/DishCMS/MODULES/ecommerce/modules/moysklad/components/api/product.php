<?php
namespace ecommerce\modules\moysklad\components\api;

use ecommerce\modules\moysklad\components\Api;

/**
 * API работы с товаром
 *
 */
class Product
{
    public function list()
    {
        return Api::i()->get('/entity/product');
    }
}