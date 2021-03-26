select 
	t.id as id, ta.id as attribute_id, ta.name, t.value
from eav_value as t 
left join eav_attribute as ta on (ta.id=t.id_attrs)
where 
	t.id_product in (select id from product where category_id=35)
group by t.value
order by ta.name, t.value;

# with descendants categories
# t.id_product in (select id from product where category_id=35 OR `category_id` IN (SELECT ... ORDER BY t.lft))
select t.id 
from category as t left join category tc on(tc.id=14) 
where t.root=tc.root and t.lft>tc.lft and t.rgt<tc.rgt and (t.`level` BETWEEN tc.`level`+1 AND tc.`level`+2) order by t.lft;

# with related category
select `rc`.`product_id` from `related_category` `rc` 
where `rc`.`category_id` in (select t.id 
