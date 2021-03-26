<?php

class m171222_091044_create_cdek_orders_table extends CDbMigration
{
	public function safeUp()
	{
        $this->createTable('cdek_orders', [
            'id'=>'pk',
            'order_id'=>'integer',
            'order_number'=>'VARCHAR(30)',
            'dispatch_number'=>'VARCHAR(32)',
            
            'send_city_id'=>'integer',
            'send_city_name'=>'string',
            'send_city_postcode'=>'integer',
            
            'rec_city_id'=>'integer',
            'rec_city_name'=>'string',
            'rec_city_postcode'=>'integer',
            'rec_name'=>'string',
            'rec_email'=>'string',
            'rec_phone'=>'string',
            
            'tariff_id'=>'integer',
            'pvz_code'=>'VARCHAR (10)',
            'pvz_data'=>'LONGTEXT',
            
            'address_street'=>'VARCHAR(50)',
            'address_house'=>'VARCHAR(30)',
            'address_flat'=>'VARCHAR(10)',
            
            'package_number'=>'VARCHAR(20)',
            'package_barcode'=>'VARCHAR(20)',
            'package_weight'=>'integer',
            
            'items'=>'LONGTEXT',
            
            'delivery_price'=>'DECIMAL(15,2)',
            'delivery_extra_charge'=>'DECIMAL(15,2)',
            'info'=>'LONGTEXT',
            'comment'=>'LONGTEXT',
            'status'=>'VARCHAR(32)',
            'create_time'=>'TIMESTAMP',
            'update_time'=>'TIMESTAMP',
        ]);
        $this->createIndex('order_id', 'cdek_orders', 'order_id', true);
	}

	public function safeDown()
	{
		echo "m171222_091044_create_cdek_orders_table does not support migration down.\n";
		//return false;
	}
}
