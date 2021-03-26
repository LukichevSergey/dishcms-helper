<?php

class m180112_091044_create_rpochta_orders_table extends CDbMigration
{
	public function safeUp()
	{
        $this->createTable('rpochta_orders', [
            'id'=>'pk',
            'order_id'=>'integer',
            'order_number'=>'VARCHAR(30)',
            'result_ids'=>'LONGTEXT',
            
            'payment_type'=>'string',
            'rpo_category'=>'string',
            'rpo_type'=>'string',
            
            'index_from'=>'integer',
            'city_name_from'=>'string',
            
            'index_to'=>'integer',
            'city_name_to'=>'string',
            'given_name'=>'string',
            'given_midname'=>'string',
            'given_surname'=>'string',
            'given_phone'=>'string',
            
            'req_data'=>'LONGTEXT',
            
            'items'=>'LONGTEXT',
            
            'mass'=>'INT(11)',
            
            'address_street'=>'VARCHAR(50)',
            'address_house'=>'VARCHAR(30)',
            'address_room'=>'VARCHAR(10)',
            'address_data'=>'LONGTEXT',
            
            'ops_address'=>'string',
            'ops_index'=>'integer',
            'ops_latitude'=>'VARCHAR(12)',
            'ops_longitude'=>'VARCHAR(12)',
            'ops_data'=>'LONGTEXT',            
                        
            'delivery_origin_price'=>'DECIMAL(15,2)',
            'delivery_price'=>'DECIMAL(15,2)',
            'delivery_mode'=>'VARCHAR(32)',
            'delivery_extra_charge'=>'DECIMAL(15,2)',
            'delivery_price_data'=>'LONGTEXT',
            
            'comment'=>'LONGTEXT',
            
            'status'=>'VARCHAR(32)',
            'create_time'=>'TIMESTAMP',
            'update_time'=>'TIMESTAMP',
        ]);
        $this->createIndex('order_id', 'rpochta_orders', 'order_id', true);
	}

	public function safeDown()
	{
		echo "m180112_091044_create_rpochta_orders_table does not support migration down.\n";
		//return false;
	}
}
